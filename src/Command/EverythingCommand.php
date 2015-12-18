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
use Epfremme\Everything\Counter\PackageVersionCounter;
use Epfremme\Everything\Entity\TestResult;
use Epfremme\Everything\Process\ProcessFactory;
use Epfremme\Everything\Filesystem\Cache;
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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

class EverythingCommand extends Command
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var ProgressBar
     */
    private $progress;

    /**
     * @var PackagistService
     */
    private $packagistService;

    /**
     * @var Collection
     */
    private $configurations;

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
     * @var Cache
     */
    private $cache;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var \SimpleXmlElement
     */
    private $phpunitXml;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('everything')
            ->setDescription('Execute tests against all composer dependencies')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->input = $input;
        $this->output = $output;

        $serializerBuilder = new SerializerBuilder();
        $serializerBuilder->configureListeners(function(EventDispatcher $eventDispatcher) {
            $eventDispatcher->addSubscriber(new SerializationSubscriber());
        });

        $this->serializer = $serializerBuilder->build();

        $this->progress = new ProgressBar($this->output);
        $this->packagistService = new PackagistService();
        $this->configurations = new Collection();
        $this->packages = new Collection();
        $this->promises = new Collection();
        $this->results = new Collection();
        $this->cache = new Cache();
        $this->json = new Json(file_get_contents('composer.json'));
        $this->phpunitXml = simplexml_load_file('phpunit.xml.dist');

        $this->progress->setFormat('very_verbose');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->fetchPackages();
        $this->prepareTests();
        $this->runTests();

        $this->printResults();

        $this->output->writeln('');
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

    private function fetchPackages()
    {
        $this->output->writeln('Fetching package versions...');
        $this->progress->start(count($this->json->getRequire()));

        foreach ($this->json->getRequire() as $package => $constraint) {
            if (strtolower($package) === 'php') {
                $this->progress->advance();
                continue;
            }

            $promise = $this->packagistService->getPackage($package)
                ->then(new Handler\Package\DeserializePackage($this->serializer))
                ->then(new Handler\Package\FilterPackageVersions($constraint))
                ->then(new Handler\Package\StorePackage($this->packages))
                ->then(new Handler\Output\AdvanceProgressBar($this->progress));

            $this->promises->push($promise);
        }
    }

    private function prepareTests()
    {
        $counter = new PackageVersionCounter($this->packages);

        $promise = $this->resolvePromises()
            ->then(new Handler\Output\CompleteProgressBar($this->output, $this->progress))
            ->then(new Handler\Output\WriteLine($this->output, 'Preparing test configurations...'))
            ->then(new Handler\Package\SortPackages($this->packages))
            ->then(new Handler\Output\StartProgressBar($this->progress, $counter))
            ->then(new Handler\Setup\GetBaseConfiguration($this->packages))
            ->then(new Handler\Setup\WriteTestConfigurations($this->packages, $this->cache, $this->json))
            ->then(new Handler\Setup\WritePhpUnitTestXml($this->cache, $this->phpunitXml))
            ->then(new Handler\Setup\CopyProjectFiles($this->cache, $this->json, $this->progress))
            ->then(new Handler\Output\CompleteProgressBar($this->output, $this->progress))
            ->wait();

        $this->promises->push($promise);
    }

    private function runTests()
    {
        $processFactory = new ProcessFactory('ls');
//        $processFactory = new ProcessFactory('nice composer install -q > /dev/null 2>&1 && bin/phpunit');
//        $processFactory = new ProcessFactory('nice bin/phpunit');
        $processManager = new ProcessManager($processFactory);
        $resultsParser = new PHPUnitResultsParser();

        $this->resolvePromises()
            ->then(new Handler\Output\WriteLine($this->output, 'Running distribution tests...'))
            ->then(new Handler\Output\StartProgressBar($this->progress, $this->cache))
            ->wait();

        $this->cache->each(function (SplFileInfo $directory) use ($processManager, $resultsParser) {
            $promise = $processManager->enqueue($directory)
                ->then(new Handler\Output\AdvanceProgressBar($this->progress))
                ->then(new Handler\Test\ParseTestResults($resultsParser))
                ->then(new Handler\Test\StoreTestResults($this->results))
                ->otherwise(new Handler\Error\HandleProcessError($this->results));

            $this->promises->push($promise);
        });

        $processManager->run(function () {
            $this->progress->display();
        });
    }

    private function printResults()
    {
        $this->resolvePromises()
            ->then(new Handler\Output\CompleteProgressBar($this->output, $this->progress))
            ->then(new Handler\Output\WriteLine($this->output, 'Printing Test Results...'))
            ->then(new Handler\Test\PrintResultsTable($this->results, $this->output))
            ->wait();
    }
}
