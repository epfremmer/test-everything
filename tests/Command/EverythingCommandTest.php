<?php
/**
 * File
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Command;

use Epfremme\Everything\Command\EverythingCommand;

/**
 * Class EverythingCommandTest
 *
 * @package Command
 */
class EverythingCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigure()
    {
        $command = new EverythingCommand('everything');

        $this->assertEquals(EverythingCommand::COMMAND_NAME, $command->getName());
        $this->assertEquals(EverythingCommand::COMMAND_DESCRIPTION, $command->getDescription());
    }
}
