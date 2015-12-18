<?php
/**
 * File Application.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Epfremme\Everything\Command\EverythingCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class Application
 *
 * @package Epfremme\Everything
 */
class Application extends BaseApplication
{
    const VERSION = '1.0.0';

    /**
     * Application constructor
     *
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('everything', self::VERSION);

        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'everything';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new EverythingCommand();

        return $defaultCommands;
    }
}
