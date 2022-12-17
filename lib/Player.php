<?php

namespace Kriptosio\Bowling\Lib;

use Kriptosio\Bowling\Lib\Turn;

class Player {
    protected $currentTurn = null;
    protected $rootTurn = null;

    protected $name = "";

    public function __construct(string $name, string $score) {
        $this->name = $name;
        $current = new Turn(1);
        $this->rootTurn = $current;
        $this->currentTurn = $current->addNextScore($score);
    }

    public function addScore(string $score) {
        $this->currentTurn = $this->currentTurn->addNextScore($score);
    }

    public function __toString() {
        $playerString = "{$this->name}\n";
        $playerString .= $this->printLines();
        return $playerString;
    }

    public function printLines() {
        $pinfalls = "Pinfalls\t";
        $score = "Score\t\t";
        $scoreSum = 0;
        for ($current = $this->rootTurn; isset($current); $current = $current->getNext()) {
            $pinfalls .=(string) $current;
            $scoreSum += $current->getScore();
            $score .= "$scoreSum\t\t";
        }

        return "$pinfalls\n$score";
    }
}
