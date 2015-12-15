<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/14/15
 * Time: 6:08 PM
 */

namespace Epfremme\Everything\Command;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Composer\Json;
use Epfremme\Everything\FileSystem\Cache;
use Epfremme\Everything\Handler;
use Epfremme\Everything\Service\PackagistService;
use Epfremme\Everything\Subscriber\SerializationSubscriber;
use GuzzleHttp\Promise as Promise;
use GuzzleHttp\Promise\PromiseInterface;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Liuggio\Fastest\Command\ParallelCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    /**
     * @var PackagistService
     */
    private $packagistService;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Collection
     */
    private $packages;

    /**
     * @var Collection|PromiseInterface[]
     */
    private $promises;

    /**
     * @var Collection
     */
    private $configurations;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $serializerBuilder = new SerializerBuilder();
        $serializerBuilder->configureListeners(function(EventDispatcher $eventDispatcher) {
            $eventDispatcher->addSubscriber(new SerializationSubscriber());
        });

        $this->serializer = $serializerBuilder->build();
        $this->packagistService = new PackagistService();
        $this->packages = new Collection();
        $this->promises = new Collection();
        $this->configurations = new Collection();
        $this->cache = new Cache();

        $this
            ->setName('everything')
            ->setDescription('Execute tests against all composer dependencies')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $json = new Json(file_get_contents('composer.json'));
        $progress = new ProgressBar($output, count($json->getRequire()));

        $output->writeln('Fetching package versions...');
        $progress->setFormat('verbose');
        $progress->start();

        foreach ($json->getRequire() as $package => $constraint) {
            if (strtolower($package) === 'php') {
                $progress->advance();
                continue;
            }

            $this->promises[] = $this->packagistService->getPackage($package)
                ->then(new Handler\DeserializePackage($this->serializer))
                ->then(new Handler\FilterPackageVersions($constraint))
                ->then(new Handler\StorePackage($this->packages))
                ->then(new Handler\AdvanceProgressBar($progress))
            ;
        }

        $this->resolvePromises()
            ->then(new Handler\WriteStepComplete($output))
            ->then(new Handler\WriteLine($output, 'Writing version configurations...'))
            ->then(new Handler\SortPackages())
            ->then(new Handler\CountPackages())
            ->then(new Handler\ResetProgressBar($progress))
            ->then(new Handler\GetBaseConfiguration($this->packages))
            ->then(new Handler\WriteTestConfigurations($this->packages, $this->cache, $progress, $json))
            ->then(new Handler\LinkProjectFiles($this->cache, $json))
            ->then(new Handler\WriteStepComplete($output))
        ;

//        $command = $this->getApplication()->find('fastest');
//        $input = new ArrayInput([
//            'command' => 'bin/phpunit',
//            'before' => 'composer install',
//            'xml' => 'phpunit.xml.dist'
//        ]);
//
//        try {
//            $command->execute($input, $output);
//        } catch (\Exception $e) {
//            var_dump($e);exit;
//        }

//        $process = new \Symfony\Component\Process\Process('find .cache/ -name "*" | vendor/bin/fastest "vendor/bin/phpunit -c app {};"');
//
//        $process->run();

//        $phpUnitXml = new \SimpleXMLElement(file_get_contents('phpunit.xml.dist'));
//
//        var_dump($phpUnitXml);
    }

    /**
     * @return PromiseInterface
     */
    private function resolvePromises()
    {
        $all = Promise\all($this->promises)->then(function() {
            return $this->packages;
        });

        $all->wait();

        return $all;
    }
}
