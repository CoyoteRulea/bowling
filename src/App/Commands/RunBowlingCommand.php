<?php
namespace Kriptosio\Bowling\App\Commands;
 
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;


use Kriptosio\Bowling\Lib\ScoreBoard;

// the "name" and "description" arguments of AsCommand replace the
// static $defaultName and $defaultDescription properties
#[AsCommand(
    name: 'app:bowling-cli',
    description: 'Prints Bowling Scores from file.',
    hidden: false,
    aliases: ['app:run']
)]
class RunBowlingCommand extends Command
{
    protected function configure()
    {
        $this->setHelp("Read a file with players and scores to print a basic scoreboard from there.\n\nThis command assume a absolute path but if file isn't loaded, try to find it on assets folder.")
             ->addArgument('filename', InputArgument::REQUIRED, 'Assign file where players and scores scores will loaded.');
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $scoreboard = new ScoreBoard($input->getArgument('filename'));

        echo (string) $scoreboard;
        
        return Command::SUCCESS;
    }
}
