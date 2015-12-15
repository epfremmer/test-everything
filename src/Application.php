<?php
/**
 * File Application.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Epfremme\Everything\Command\TestCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class Application
 *
 * @package Epfremme\Everything
 */
class Application extends \Liuggio\Fastest\Application
{
    /**
     * Application constructor
     *
     * {@inheritdoc}
     */
    public function __construct()
    {
        BaseApplication::__construct('everything', self::VERSION);

        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'everything';
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new TestCommand();

        return $defaultCommands;
    }
}
