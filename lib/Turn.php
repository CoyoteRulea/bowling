<?php

namespace Kriptosio\Bowling\Lib;

use Exception;

class Turn {
    protected $left = null;
    protected $right = null;
    protected $extra = null;

    protected $turnNumber;

    protected ?Turn $next = null;

    public function __construct(int $turn) {
        $this->turnNumber = $turn;
    }

    public function addNextScore(string $value) : Turn {
            
        if ($this->turnNumber == 10) {
            if (!isset($this->left)) {
                $this->left = $value;
            } elseif (!isset($this->right)) {
                $this->right = $value;
            } elseif ($this->left == '10') {
                $this->extra = $value;
            } else {
                throw new Exception("ERROR: An Unexpected Error Ocurred. Turn 10 only can have a extra shoot if first shoot in turn 10 is strike.");
            }

            return $this;
        } 

        if (!isset($this->left) && (int) $value < 10) {
            // First shot case and not strike
            $this->left = $value;
        } else {
            // Previous shot already done
            if (isset($this->left) && (int) $this->left + (int) $value > 10) {
                throw new Exception("ERROR: An Unexpected Error Ocurred. Lines can't have more than 10 pines per turn.");
            }

            // Strike or second shot value 
            $this->right = $value;
        }
        
        // Check if not is the last turn and all scores already assigned for this turn
        if ($this->turnNumber != 10 && isset($this->right)) {
            $this->next = new Turn($this->turnNumber + 1);
            return $this->next;
        }

        return $this;
    }

    public function __toString() {
        if ($this->turnNumber == 10) {
            $left = $this->left == '10' ? 'X' : $this->left;
            $right = $this->right == '10' ? 'X' : $this->right;
            $extra = isset($this->extra) ? ($this->extra == '10' ? 'X' : $this->extra) : ' '; 

            return "$left\t$right\t$extra";
        }

        $left  = isset($this->left) ? $this->left : ' ';
        $right = isset($this->left) ? ((int) $this->left + (int) $this->right == 10 ? '/' : $this->right) : ($this->right == '10' ? 'X' : $this->right);
        
        return "$left\t$right\t";
    }

    public function getScore() : int {
        $left = (int) $this->left;
        $right = (int) $this->right;

        $extrapoints = 0;
        $sum = 0;
        if ($this->turnNumber != 10) {
            if ($this->right == '10') {
                $extrapoints = 2;
            } elseif ($left + $right == 10) {
                $extrapoints = 1;
            }

            if ($extrapoints == 0) {
                return $left + $right;
            } else {
                $sum = $left + $right;
                for ($x = 0, $current = $this; $x < $extrapoints; $x++) {
                    $current = $current->getNext();
                    if (!isset($current->left)) {
                        $sum += (int) $current->right;
                    } else {
                        $sum += (int) $current->left;
                        $x++;
                        if ($x < $extrapoints)
                            $sum += (int) $current->right;
                    }              
                }
            }
        } else {
            return $left + $right + (int) $this->extra;
        }

        return $sum;
    }

    public function &getNext() : ?Turn {
        return $this->next;
    }
}
