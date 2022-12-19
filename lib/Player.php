<?php

namespace Kriptosio\Bowling\Lib;

use Kriptosio\Bowling\Lib\Turn;

class Player {
    // List variables are protected to avoid indesired changes 
    // those what can affect logic in scoreboard points logic
    protected $currentTurn = null;
    protected $rootTurn = null;

    protected $name = "";

    // Width constant in chars for prettyfy
    const TURN_SPACE  = 5;
    const SCORE_SPACE = (self::TURN_SPACE * 2) + 1;
    // Char constants for prettyfy
    const CHAR_BAR          = '─';
    const CHAR_FOOTERBEGIN  = '└';
    const CHAR_FOOTERMIDDLE = '┴';
    const CHAR_FOOTEREND    = '┘';
    const CHAR_HEADERBEGIN  = '┌';
    const CHAR_HEADERMIDDLE = '┬';
    const CHAR_HEADEREND    = '┐';
    const CHAR_ITEMBEGIN    = '├';
    const CHAR_ITEMMIDDLE   = '┼';
    const CHAR_ITEMEND      = '┤';
    const CHAR_PIPE         = '│';
    const CHAR_SPACE        = ' ';

    /**
     * Constructor of this class
     * 
     * @param string name Required to show this player name
     * @param string score First node score
     */
    public function __construct(string $name, string $score) {
        $this->name = $name;

        // Every node Turn contains their turnNumber to simplify the score points logic
        $current = new Turn(1);
        $this->rootTurn = $current;
        $this->currentTurn = $current->addNextScore($score);
    }

    /**
     * Add the next score in order to ten pin bowling logic and move the logic pointer
     * 
     * @param string $score current score to be inserted 
     */
    public function addScore(string $score) {
        $this->currentTurn = $this->currentTurn->addNextScore($score);
    }

    /**
     * This object represented like string
     * 
     * @return string a string with player lines values separated by tabs
     */
    public function __toString() : string {
        $playerString = "{$this->name}\n";
        $playerString .= $this->printLines();
        return $playerString;
    }

    /**
     * Get the pines down on every single turn and their score
     * 
     * @return string a string with turns and score values separated by tabs
     */
    public function printLines() : string {
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

    /**
     * Print current player lines
     * 
     * @return string not enclosed table with current player name, lines  and scores
     */
    public function printPrettyLines() : string {
        $itemEnd = '';
        $headEnd = '';

        $scoreSum = 0;
        
        // Row Description
        $lineblock = $this->lineBlock(self::SCORE_SPACE, self::CHAR_BAR);

        // create main lines to add tables in 
        $headerBlock = self::CHAR_ITEMBEGIN . $lineblock . self::CHAR_HEADERMIDDLE;
        $footerBlock = self::CHAR_ITEMBEGIN . $lineblock . self::CHAR_FOOTERMIDDLE;
        $turnBlock = self::CHAR_ITEMBEGIN . $lineblock . self::CHAR_ITEMMIDDLE;
        $pinfalls = self::CHAR_PIPE . str_pad('PINFALLS', self::SCORE_SPACE, self::CHAR_SPACE, STR_PAD_BOTH) . self::CHAR_PIPE;
        $score = self::CHAR_PIPE . str_pad('SCORE', self::SCORE_SPACE, self::CHAR_SPACE, STR_PAD_BOTH) . self::CHAR_PIPE;
        $name  = self::CHAR_PIPE . str_pad($this->name, $this->getLineWidth(), self::CHAR_SPACE, STR_PAD_BOTH) . self::CHAR_PIPE;

        // List all scores on this player to show pinfalls and scores  
        for ($current = $this->rootTurn, $turn = 1; isset($current); $current = $current->getNext(), $turn++) {
            switch ($turn) {
                case 10:
                    // If this is the last node
                    $footEnd = $headEnd = $itemEnd = self::CHAR_ITEMEND;
                    
                    $lineblock .= $this->lineBlock(self::TURN_SPACE + 1, self::CHAR_BAR);
                    
                    $scorespace = self::SCORE_SPACE + self::TURN_SPACE + 1;
                    break;
                default:
                    $headEnd = self::CHAR_HEADERMIDDLE;
                    $footEnd = self::CHAR_FOOTERMIDDLE;
                    $itemEnd = self::CHAR_ITEMMIDDLE;
                    $scorespace = self::SCORE_SPACE;
                    break;
            }
            
            $headerBlock .= $lineblock . $headEnd;
            $footerBlock .= $lineblock . $footEnd;
            $turnBlock   .= $lineblock . $itemEnd;

            // Get pin falls on this turn
            $pinFallValues = explode("\t", (string) $current);
            for ($currentValue = 0; $currentValue < ($turn == 10 ? 3 : 2); $currentValue++) {
                $pinfalls .= str_pad($pinFallValues[$currentValue], self::TURN_SPACE, self::CHAR_SPACE, STR_PAD_BOTH) . self::CHAR_PIPE;
            }
            $scoreSum += $current->getScore();
            $score .= str_pad($scoreSum, $scorespace, self::CHAR_SPACE, STR_PAD_BOTH) . self::CHAR_PIPE;
        }

        return  <<<PLAYER
                $name
                $headerBlock
                $pinfalls
                $turnBlock
                $score
                $footerBlock
                PLAYER;
    }

    /**
     * Prints a footer for prettified scoreboard
     * 
     * @param string $text (optional) assigns a text to be showed in footer
     * 
     * @return string a string with prettified footer
     */
    public static function printPrettyFooter(string $text = null) : string {
        
        $lineblock    = self::lineBlock(self::getLinewidth(), self::CHAR_BAR);
        $footerBlock  = self::CHAR_PIPE . str_pad($text, self::getLinewidth(), self::CHAR_SPACE, STR_PAD_LEFT) . self::CHAR_PIPE . "\n";
        $footerBlock .= self::CHAR_FOOTERBEGIN . $lineblock . self::CHAR_FOOTEREND;
 
        return "\n$footerBlock\n";       
    }
    /**
     * Prints a header for prettified scoreboard
     * 
     * @param string $text (optional) assigns a text to be showed in header
     * 
     * @return string a string with prettified header
     */
    public static function printPrettyHeader(string $text = 'Frame') : string {
        
        $lineblock = self::lineBlock(self::SCORE_SPACE, self::CHAR_BAR);
        $titleBlock = self::CHAR_PIPE;
        $headerTop = self::CHAR_HEADERBEGIN . $lineblock . self::CHAR_HEADERMIDDLE;
        $headerBottom = self::CHAR_ITEMBEGIN . $lineblock . self::CHAR_FOOTERMIDDLE;
        for ($x = 0; $x < 11; $x++) {

            $currentSpace = self::SCORE_SPACE;
            $currentText = $x;
            switch ($x) {
                case 0:
                    $currentText = $text;
                    break;
                case 10:
                    $currentSpace = self::SCORE_SPACE + self::TURN_SPACE + 1;
                    
                    $lineblock .= self::lineBlock(self::TURN_SPACE + 1, self::CHAR_BAR);
                    $headerTop .= $lineblock . self::CHAR_HEADEREND;
                    $headerBottom .= $lineblock . self::CHAR_ITEMEND; 
                    break;
                default:
                    $headerTop .= $lineblock . self::CHAR_HEADERMIDDLE;
                    $headerBottom .= $lineblock . self::CHAR_FOOTERMIDDLE;     
                    
            }
            $titleBlock .= str_pad($currentText, $currentSpace, self::CHAR_SPACE, STR_PAD_BOTH) . self::CHAR_PIPE;
        }
        
        return <<<HEADER
                $headerTop
                $titleBlock
                $headerBottom
                HEADER;       
    }

    /**
     * Get the width in pixels to create headers and table bars.
     * 
     * @return int size of with of current scoreboard
     */
    public static function getLinewidth() : int {
        // 23 it's the number of times single score (turn space plue pipe char) fits in wide
        // minus first pipe.
        return (self::TURN_SPACE + 1) * 23 - 1;
    }

    /**
     * Return a string with specific size and repeated 
     * 
     * @param int $size width of the returned string
     * @param string $char character to be repeated
     * 
     * @return string string with a line block
     */
    protected static function lineBlock(int $size, string $char) : string {
        
        $lineblock = '';

        for ($position = 0; $position < $size; $position++) $lineblock .= $char;

        return $lineblock;
    }
}
