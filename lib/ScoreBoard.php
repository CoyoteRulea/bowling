<?php

namespace Kriptosio\Bowling\Lib;

use Kriptosio\Bowling\Lib\Player;

use Exception;

class ScoreBoard {
    protected $players = [];

    /**
     * Creates a scoreboard item with values laoded from a file
     * 
     * @param string $filename a file with the players name and info for pin falls every turn
     */
    public function __construct(string $filename) {
        $tryFileName = $filename;

        // If file assigned is not the absolute filename
        if (!file_exists($tryFileName)) {
            // try to search it on assets folder
            $tryFileName = __DIR__ . '/../assets/' . $filename;

            if (!file_exists($tryFileName)) {
                throw new Exception(sprintf(Glossary::ERROR_UNABLE_TO_OPEN_FILE, $filename), 10);
            }
        }

        $file = fopen($tryFileName, "r");

        //Output lines until EOF is reached
        for ($x = 1; !feof($file); $x++) {
            $line = explode(' ', fgets($file));

            $lineElements = count($line);
            if ($lineElements > 2) {
                // If one line contains more elements than expected
                throw new Exception(sprintf(Glossary::ERROR_REQUIRED_FORMAT_FAIL, $filename, $x), 20);
            }
            elseif ($lineElements == 1) {
                // EOF
                continue;
            }
            
            if (!isset($this->players[$line[0]])) {
                // If player doesn't exist creates a new one
                $this->players[$line[0]] = new Player($line[0], rtrim($line[1]));
            } else {
                // otherwise assing a new shoot pin fall
                $this->players[$line[0]]->addScore(rtrim($line[1]));
            }
        }

        fclose($file);
    }

    /**
     * returns a representation of this scoreboard in a simple format
     */
    public function __toString() {
        // Print header
        $scoreString = "Frame\t\t";
        for ($x = 1; $x <= 10; $x++) {
            $scoreString .= "$x\t\t";
        }

        // Print players pinfalls and scores
        foreach($this->players as $player) {
            $scoreString .= "\n" . (string) $player;
        }

        return $scoreString;
    }

    /**
     * Display this scoreboard in a easy to read format
     * 
     * @return string returns a string with an easy to read score
     */
    public function printPrettyScore() {
        $scoreString = Player::printPrettyHeader();
        // Print players pinfalls and scores
        foreach($this->players as $player) {
            $scoreString .= "\n" . $player->printPrettyLines();
        }

        $scoreString .= Player::printPrettyFooter('*** This is a Demo for KriptosIO Code Challenge.');

        return $scoreString;
    }

    /**
     * Returns number of players on this scoreboard
     * 
     * @return int player count on this scoreboard
     */
    public function getPlayersCount() : int {
        return count($this->players);
    }

    /**
     * Get Player Objects list
     * 
     * @return array Array of player objects in your scoreboard
     */
    public function getPlayers() : array {
        return $this->players;
    }
}
