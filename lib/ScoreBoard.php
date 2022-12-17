<?php

namespace Kriptosio\Bowling\Lib;

use Kriptosio\Bowling\Lib\Player;

use Exception;

class ScoreBoard {
    private $players = [];

    public function __construct(string $filename) {
        $tryFileName = $filename;

        // If file assigned is not the absolute filename
        if (!file_exists($tryFileName)) {
            $tryFileName = __DIR__ . '/../assets/' . $filename;

            if (!file_exists($tryFileName))
                    throw new Exception("ERROR: Unable to load file $filename.");
        }

        $file = fopen($tryFileName, "r");

        //Output lines until EOF is reached
        for ($x = 1; !feof($file); $x++) {
            $line = explode(' ', fgets($file));

            $lineElements = count($line);
            if ($lineElements > 2)
                // If one line contains more elements than expected
                throw new Exception("ERROR: Required format fail on $filename at line $x.");
            elseif ($lineElements == 1) 
                // EOF
                continue;
                
            if (!isset($this->players[$line[0]])) {
                $this->players[$line[0]] = new Player($line[0], rtrim($line[1]));
            } else {
                $this->players[$line[0]]->addScore(rtrim($line[1]));
            }
        }

        fclose($file);
    }

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
}
