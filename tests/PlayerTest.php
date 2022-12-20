<?php
namespace Kriptosio\Bowling;

use PHPUnit\Framework\TestCase;
use Kriptosio\Bowling\Lib\Glossary;
use Kriptosio\Bowling\Lib\Functions;
use Kriptosio\Bowling\Lib\Player;

/**
 * @covers Kriptosio\Bowling\Lib\Player
 * @covers Kriptosio\Bowling\Lib\Turn
 */
final class PlayerTest extends TestCase
{
    protected static $playerNames = [];

    /**
     * Setup initial variables for PlayerTest
     */
    public static function setUpBeforeClass() : void {

        self::$playerNames = Functions::getColumFromCSV(__DIR__ . Glossary::SUCCESS_FILES['THREE_PLAYERS'], 0, true);
    }

    /**
     * Test player class constructor
     */
    public function testCreatePlayerSuccess() {
        
        $playerName = self::$playerNames[mt_rand(0, 2)];
        $score = mt_rand(0, 9);
        $playerObj = new Player($playerName, $score);
        $stats = $playerObj->getPlayerStats();

        $this->assertInstanceOf('Kriptosio\Bowling\Lib\Player', $playerObj);
        $this->assertEquals($playerName, $playerObj->getPlayerName());
        $this->assertEquals("$score\t\t", $stats['pinfalls']);
        $this->assertEquals("$score\t\t", $stats['scores']);
    }

    /**
     * Test to object to string convertion
     */
    public function testCastToStringSuccess() {
        $playerName = self::$playerNames[mt_rand(0, 2)];
        $score = mt_rand(0, 9);
        $playerObj = new Player($playerName, $score);
        $stats = $playerObj->getPlayerStats();

        $toString = explode("\n", (string) $playerObj);
        $this->assertEquals($playerName, $toString[0]);
        $this->assertEquals("Pinfalls\t$score\t\t", $toString[1]);
        $this->assertEquals("Score\t\t$score\t\t", $toString[2]);
    }

    /**
     * Test lineblock method
     */
    public function testLineblockSuccess() {
        $char  = chr(mt_rand(33, 126));
        $space = Player::SCORE_SPACE; 

        $lineBlock = Player::lineBlock($space, $char);

        $this->assertEquals($space, strlen($lineBlock));
        $this->assertEquals($char,  substr($lineBlock, mt_rand(0, $space - 1), 1));
    }
}
