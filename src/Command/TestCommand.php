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
use Epfremme\Everything\Entity\TestResult;
use Epfremme\Everything\Process\ProcessFactory;
use Epfremme\Everything\FileSystem\Cache;
use Epfremme\Everything\Handler;
use Epfremme\Everything\Parser\PHPUnitResultsParser;
use Epfremme\Everything\Process\ProcessManager;
use Epfremme\Everything\Service\PackagistService;
use Epfremme\Everything\Subscriber\SerializationSubscriber;
use GuzzleHttp\Promise as Promise;
use GuzzleHttp\Promise\PromiseInterface;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

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
     * @var Collection|TestResult[]
     */
    private $results;

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
        $this->results = new Collection();
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
        $phpunitXml = simplexml_load_file('phpunit.xml.dist');

        $output->writeln('Fetching package versions...');
        $progress->setFormat('very_verbose');
        $progress->start();

        foreach ($json->getRequire() as $package => $constraint) {
            if (strtolower($package) === 'php') {
                $progress->advance();
                continue;
            }

            $promise = $this->packagistService->getPackage($package)
                ->then(new Handler\Package\DeserializePackage($this->serializer))
                ->then(new Handler\Package\FilterPackageVersions($constraint))
                ->then(new Handler\Package\StorePackage($this->packages))
                ->then(new Handler\Output\AdvanceProgressBar($progress))
            ;

            $this->promises->push($promise);
        }

        $this->resolvePromises()
            ->then(new Handler\Output\CompleteProgressBar($output, $progress))
            ->then(new Handler\Output\WriteLine($output, 'Preparing test configurations...'))
            ->then(new Handler\Package\SortPackages($this->packages))
            ->then(new Handler\Package\CountPackageVersions($this->packages))
            ->then(new Handler\Output\StartProgressBar($progress))
            ->then(new Handler\Setup\GetBaseConfiguration($this->packages))
            ->then(new Handler\Setup\WriteTestConfigurations($this->packages, $this->cache, $json))
            ->then(new Handler\Setup\WritePhpUnitTestXml($this->cache, $phpunitXml))
            ->then(new Handler\Setup\CopyProjectFiles($this->cache, $json, $progress))
            ->then(new Handler\Output\CompleteProgressBar($output, $progress))
            ->then(new Handler\Output\WriteLine($output, 'Running distribution tests...'))
            ->then(new Handler\Package\CountPackageVersions($this->packages))
            ->then(new Handler\Output\StartProgressBar($progress))
            ->wait()
        ;

//        $processFactory = new ProcessFactory('ls');
        $processFactory = new ProcessFactory('nice composer install -q > /dev/null 2>&1 && bin/phpunit');
        $processManager = new ProcessManager($processFactory);
        $resultsParser = new PHPUnitResultsParser();

        $this->cache->each(function(SplFileInfo $directory) use ($processManager, $resultsParser, $progress) {
            $promise = $processManager->enqueue($directory)
                ->then(new Handler\Output\AdvanceProgressBar($progress))
                ->then(new Handler\Test\ParseTestResults($resultsParser))
                ->then(new Handler\Test\StoreTestResults($this->results))
                ->otherwise(new Handler\Error\HandleProcessError($this->results))
            ;

            $this->promises->push($promise);
        });

        $processManager->run(function() use ($progress) {
            $progress->display();
        });

        $this->resolvePromises()
            ->then(new Handler\Output\CompleteProgressBar($output, $progress))
            ->then(new Handler\Output\WriteLine($output, 'Printing Test Results...'))
            ->then(new Handler\Test\PrintResultsTable($this->results, $output))
            ->wait()
        ;

        $output->writeln('');
    }

    /**
     * @return PromiseInterface
     */
    private function resolvePromises()
    {
        /** @var PromiseInterface $all */
        $all = Promise\all($this->promises);

        $all->wait();
        $this->promises->clear();

        return $all;
    }
}
