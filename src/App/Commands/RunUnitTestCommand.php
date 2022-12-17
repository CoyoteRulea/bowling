<?php
namespace Kriptosio\Bowling\App\Commands;
 
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Attribute\AsCommand;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

// the "name" and "description" arguments of AsCommand replace the
// static $defaultName and $defaultDescription properties
#[AsCommand(
    name: 'app:run-tests',
    description: 'Run PHPUnit Test for Bowling APP.',
    hidden: false,
    aliases: ['app:tests']
)]
class RunUnitTestCommand extends Command
{
    protected function configure()
    {
        $this->setHelp("Run all Tests declared in your PHPUnit folder.");
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Execute PHPUnit All tests in folder Tests
        $process = new Process(['./vendor/bin/phpunit', 'Tests']);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
        return Command::SUCCESS;
    }
}