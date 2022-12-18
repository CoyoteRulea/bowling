<?php

namespace Kriptosio\Bowling\Lib;

use Kriptosio\Bowling\Lib\Turn;

class Player {
    protected $currentTurn = null;
    protected $rootTurn = null;

    protected $name = "";

    // Width constant in chars
    const TURN_SPACE  = 5;
    const SCORE_SPACE = (self::TURN_SPACE * 2) + 1;

    const CHAR_BAR          = '─';
    const CHAR_PIPE         = '│';
    const CHAR_ITEMBEGIN    = '├';
    const CHAR_HEADERBEGIN  = '┌';
    const CHAR_HEADERMIDDLE = '┬';
    const CHAR_HEADEREND    = '┐';
    const CHAR_FOOTERBEGIN  = '└';
    const CHAR_FOOTERMIDDLE = '┴';
    const CHAR_FOOTEREND    = '┘';
    const CHAR_ITEMMIDDLE   = '┼';
    const CHAR_ITEMEND      = '┤';
    const CHAR_SPACE        = ' ';

    public function __construct(string $name, string $score) {
        $this->name = $name;
        $current = new Turn(1);
        $this->rootTurn = $current;
        $this->currentTurn = $current->addNextScore($score);
    }

    /**
     * Add the next score in order to ten pin bowling logic and move the logic pointer
     * @param score 
     *
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
        for ($x = 0, $lineblock = ''; $x < self::SCORE_SPACE; $x++, $lineblock .= self::CHAR_BAR);

        // create main lines to add tables in 
        $headerBlock = self::CHAR_ITEMBEGIN . $lineblock . self::CHAR_HEADERMIDDLE;
        $footerBlock = self::CHAR_ITEMBEGIN . $lineblock . self::CHAR_FOOTERMIDDLE;
        $turnBlock = self::CHAR_ITEMBEGIN . $lineblock . self::CHAR_ITEMMIDDLE;
        $pinfalls = self::CHAR_PIPE . str_pad('PINFALLS', self::SCORE_SPACE, self::CHAR_SPACE, STR_PAD_BOTH) . self::CHAR_PIPE;
        $score = self::CHAR_PIPE . str_pad('SCORE', self::SCORE_SPACE, self::CHAR_SPACE, STR_PAD_BOTH) . self::CHAR_PIPE;
        $name  = self::CHAR_PIPE . str_pad($this->name, $this->getLineWidth(), self::CHAR_SPACE, STR_PAD_BOTH) . self::CHAR_PIPE;
        
        for ($current = $this->rootTurn, $x = 1; isset($current); $current = $current->getNext(), $x++) {
            switch ($x) {
                case 10:
                    $footEnd = $headEnd = $itemEnd = self::CHAR_ITEMEND;
                    
                    for ($y = 0; $y <= self::TURN_SPACE; $y++, $lineblock .= self::CHAR_BAR);
                    
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

            $pinFallValues = explode("\t", (string) $current);
            for ($y = 0; $y < ($x == 10 ? 3 : 2); $y++) {
                $pinfalls .= str_pad($pinFallValues[$y], self::TURN_SPACE, self::CHAR_SPACE, STR_PAD_BOTH) . self::CHAR_PIPE;
            }
            $scoreSum += $current->getScore();
            $score .= str_pad($scoreSum, $scorespace, self::CHAR_SPACE, STR_PAD_BOTH) . self::CHAR_PIPE;
        }

        return "$name\n$headerBlock\n$pinfalls\n$turnBlock\n$score\n$footerBlock";
    }

    public static function printPrettyFooter(string $text = null) : string {
        
        for ($y = 0, $lineblock = '', $width = self::getLinewidth(); $y < $width; $y++, $lineblock .= self::CHAR_BAR);
        $footerBlock = self::CHAR_PIPE . str_pad($text, self::getLinewidth(), self::CHAR_SPACE, STR_PAD_LEFT) . self::CHAR_PIPE . "\n";
        $footerBlock .= self::CHAR_FOOTERBEGIN . $lineblock . self::CHAR_FOOTEREND;

        return "\n$footerBlock\n";       
    }

    public static function printPrettyHeader(string $text = null) : string {
        for ($y = 0, $lineblock = '', $width = self::SCORE_SPACE; $y < $width; $y++, $lineblock .= self::CHAR_BAR);
        $titleBlock = self::CHAR_PIPE;
        $headerTop = self::CHAR_HEADERBEGIN . $lineblock . self::CHAR_HEADERMIDDLE;
        $headerBottom = self::CHAR_ITEMBEGIN . $lineblock . self::CHAR_FOOTERMIDDLE;
        for ($x = 0; $x < 11; $x++) {

            $currentSpace = self::SCORE_SPACE;
            $currentText = $x;
            switch ($x) {
                case 0:
                    $currentText = isset($text) ? $text : 'Frame';
                    break;
                case 10:
                    $currentSpace = self::SCORE_SPACE + self::TURN_SPACE + 1;
                    
                    for ($y = 0, $turnBlock = '', $width = self::TURN_SPACE + 1; $y < $width; $y++, $turnBlock .= self::CHAR_BAR);
                    $headerTop .= $lineblock . $turnBlock . self::CHAR_HEADEREND;
                    $headerBottom .= $lineblock . $turnBlock . self::CHAR_ITEMEND; 
                    break;
                default:
                    $headerTop .= $lineblock . self::CHAR_HEADERMIDDLE;
                    $headerBottom .= $lineblock . self::CHAR_FOOTERMIDDLE;     
                    
            }
            $titleBlock .= str_pad($currentText, $currentSpace, self::CHAR_SPACE, STR_PAD_BOTH) . self::CHAR_PIPE;
        }
        
        return "\n$headerTop\n$titleBlock\n$headerBottom";       
    }

    /**
     * Get the width in pixels to create headers and table bars.
     */
    public static function getLinewidth() {
        // 23 it's the number of times single score (turn space plue pipe char) fits in wide
        // minus first pipe.
        return (self::TURN_SPACE + 1) * 23 - 1;
    } 
}
