<?php
namespace Kriptosio\Bowling\App\Commands;
 
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

// the "name" and "description" arguments of AsCommand replace the
// static $defaultName and $defaultDescription properties
#[AsCommand(
    name: 'app:run-tests',
    description: 'Run PHPUnit Tests for Bowling APP.',
    hidden: false,
    aliases: ['app:tests']
)]
class RunUnitTestCommand extends Command
{
    const TestFolder = 'tests';

    protected function configure()
    {
        $this->setHelp("Run all Tests declared in your PHPUnit folder.")
             ->addOption(
                'testlist',
                't',
                InputOption::VALUE_OPTIONAL,
                'Pass the comma separated test list if you don\'t want to execute all tests.',
                ''
             );
    }
 
    /**
     * Execute all PHP Unit Tests
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('testlist'))
        {
            $list = explode(",", $input->getOption('testlist'));
            $testresponse = "\n";

            $progressBar = new ProgressBar($output, count($list));
            $progressBar->start();
            
            if (is_array($list) && count($list))
            {
                foreach ($list as $item)
                {
                    $banner = <<<BANNER
                                Test Response for $item
                                ====================================================================================
                                
                                BANNER;
                    $testresponse .= $banner;
                    $testresponse .= "{$this->runTestProcess($item)}\n";
                    $progressBar->advance();
                }
            }

            $progressBar->finish();
            echo $testresponse;
        } else {
            echo $this->runTestProcess();
        }

        return Command::SUCCESS;
    }

    protected function runTestProcess(string $test = null) {
        $process = new Process(['./vendor/bin/phpunit', (!isset($test) ? self::TestFolder : self::TestFolder . "/$test.php")]);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        
        return $process->getOutput();
    }
}
