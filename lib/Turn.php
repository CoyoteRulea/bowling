<?php
namespace Kriptosio\Bowling\Lib;

use Exception;

class Turn {
    protected $left = null;
    protected $right = null;
    protected $extra = null;

    protected $turnNumber;

    protected ?Turn $next = null;

    /**
     * Turn exceptions
     */
    const ERROR_INVALID_10TH_TURN = 'ERROR: Something was wrong trying to create an invalid 10 turn number (only 1-10 are allowed).';
    const ERROR_MORE_THAN_10TURNS = 'ERROR: An Unexpected Error Ocurred. Score file contains more than 10 turns.';
    const ERROR_INVALIDEXTRASHOOT = 'ERROR: An Unexpected Error Ocurred. Turn 10 only can have a extra shoot if first shoot in turn 10 is strike.';
    const ERROR_MORE_THAN10POINTS = 'ERROR: An Unexpected Error Ocurred. Lines can\'t have more than 10 pines per turn.';
    /**
     * Creates a new turn node 
     * 
     * @param int $turn specificies which turn has the current node
     */
    public function __construct(int $turn) {

        if ($turn < 1 || $turn > 10) {
            // Something was wrong with turns and isn't allowed containing more than 10 turns allowed
            throw new Exception(self::ERROR_INVALID_10TH_TURN, $turn);
        }
        // Creates a new turn and specifies which turn 
        $this->turnNumber = $turn;
    }

    /**
     * Check for the next score to be inserted and determines if needs to be added in new node or could be inserted in current
     * 
     * @param string $value Value of current score
     * 
     * @return Turn Returns pointer for current turn element in the list
     */
    public function addNextScore(string $value) : Turn {
        // Check if this is the last turn, to check specific rules and avoid to create a 11th node.
        if ($this->turnNumber == 10) {
            if (!isset($this->left)) {
                $this->left = $value;
            } elseif (!isset($this->right)) {
                $this->right = $value;
            } elseif ($this->left == '10') {
                // Check if a third value is allowed according to rules
                if (!isset($this->extra)) {
                    $this->extra = $value;
                } else {
                    // Something was wrong with turns and isn't allowed containing more than 10 turns allowed
                    throw new Exception(self::ERROR_MORE_THAN_10TURNS);
                }
            } else {
                // Something was wrong with turns and isn't allowed to insert a third value
                throw new Exception(self::ERROR_INVALIDEXTRASHOOT);
            }

            return $this;
        } 

        // For simple turns
        if (!isset($this->left) && (int) $value < 10) {
            // First shot case and not strike
            $this->left = $value;
        } else {
            // Previous shot already done
            if (isset($this->left) && (int) $this->left + (int) $value > 10) {
                throw new Exception(self::ERROR_MORE_THAN10POINTS);
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

    /**
     * This object represented like string
     * 
     * @return string a string with score values separated by tabs
     */
    public function __toString() : string {
        if ($this->turnNumber == 10) {
            $left  = $this->left == '10' ? 'X' : $this->left;
            $right = $this->right == '10' ? 'X' : $this->right;
            $extra = isset($this->extra) ? ($this->extra == '10' ? 'X' : $this->extra) : ' '; 

            return "$left\t$right\t$extra";
        }

        $left  = isset($this->left) ? $this->left : ' ';
        $right = isset($this->left) ? ((int) $this->left + (int) $this->right == 10 ? '/' : $this->right) : ($this->right == '10' ? 'X' : $this->right);
        
        return "$left\t$right\t";
    }

    /**
     * Check current node to analize current score value according to ten-pin bowling rules
     * 
     * @return int value with score for current node
     */
    public function getScore() : int {
        // Get integer value of each element
        $left = (int) $this->left;
        $right = (int) $this->right;

        $extrapoints = 0;
        $sum = 0;
        // For all nodes check if strike or spare to add extra points
        if ($this->turnNumber != 10) {
            if ($this->right == '10' && !isset($this->left)) {
                // In case of strike
                $extrapoints = 2;
            } elseif ($left + $right == 10) {
                // In case of spare
                $extrapoints = 1;
            }

            // If no extra points needed only calculate direct points
            if ($extrapoints == 0) {
                return $left + $right;
            } else {
                // add ten posible points
                $sum = 10;
                for ($x = 0, $current = $this; $x < $extrapoints; $x++) {
                    // Move to the next node
                    $current = $current->getNext();
                    if (!isset($current->left)) {
                        // If strike
                        $sum += (int) $current->right;
                    } else {
                        // Spare
                        $sum += (int) $current->left;
                        $x++;
                        // Check if need to check next value
                        if ($x < $extrapoints)
                            $sum += (int) $current->right;
                    }              
                }
            }
        } else {
            // Last node case
            return $left + $right + (int) $this->extra;
        }

        return $sum;
    }

    /**
     * Access to next element in list
     * 
     * @return Turn pointer to next element
     */
    public function &getNext() : ?Turn {

        return $this->next;
    }

    /**
     * Get turn number
     * 
     * @return int turn number of current node
     */
    public function getTurnNumber() : int {

        return $this->turnNumber;
    }
}
