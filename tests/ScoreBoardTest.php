<?php
namespace Kriptosio\Bowling;

use PHPUnit\Framework\TestCase;
use Kriptosio\Bowling\Lib\Functions;
use Kriptosio\Bowling\Lib\Glossary;
use Kriptosio\Bowling\Lib\Player;
use Kriptosio\Bowling\Lib\Turn;
use Kriptosio\Bowling\Lib\ScoreBoard;

/**
 * @covers Kriptosio\Bowling\Lib\ScoreBoard
 */
final class ScoreBoardTest extends TestCase
{    
    /**
     * Test a valid score boards files
     *      
     * @covers Kriptosio\Bowling\Lib\Functions
     * @covers Kriptosio\Bowling\Lib\Player
     * @covers Kriptosio\Bowling\Lib\Turn
     */
    public function testScoreBoardSuccess() {
        $lineWidth = Player::getLineWidth() + 2;

        // Test all valid cases
        foreach (Glossary::SUCCESS_FILES as $key => $file) {
            $filewithpath = __DIR__ . $file;

            // Create a new ScoreBoard OBject and validate
            $scoreBoardObj = new ScoreBoard($filewithpath);
            $this->assertInstanceOf('Kriptosio\Bowling\Lib\ScoreBoard', $scoreBoardObj);

            // ScoreBoard has same number of players than obtained directly from CSV
            $playersNumber = $scoreBoardObj->getPlayersCount();
            $playersCSV = Functions::getColumFromCSV($filewithpath, 0, true);
            $this->assertEquals(count($playersCSV), $playersNumber);

            // Get the player unique names list and validate with ScoreBoard players array            
            $playersObj = $scoreBoardObj->getPlayers();
            $this->assertIsArray($playersCSV);

            for ($item = 0; $item < $playersNumber; $item++) {
                $this->assertArrayHasKey($playersCSV[$item], $playersObj);
            }

            // Validate tab separated lines
            $lines = mb_split("\n", (string) $scoreBoardObj);
            $totalLines = 3 * $playersNumber + 1;
            $this->assertEquals(count($lines), $totalLines);

            // Validate pretty lines
            $prettyLines = mb_split("(\r\n|\n|\r)", rtrim($scoreBoardObj->printPrettyScore(), "\n"));
            $totalLines = 6 * $playersNumber + 5;
            $this->assertEquals(count($prettyLines), $totalLines);

            for ($line = 0; $line < $totalLines; $line++) {
                $this->assertEquals($lineWidth, mb_strlen($prettyLines[$line]));
                switch ($line) {
                    case 0:
                        // check header border chars
                        $this->assertEquals(Player::CHAR_HEADERBEGIN, mb_substr($prettyLines[$line], 0, 1));
                        $this->assertEquals(Player::CHAR_HEADEREND, mb_substr($prettyLines[$line], -1));
                        break;
                    case $totalLines - 1:
                        // check header border chars
                        $this->assertEquals(Player::CHAR_FOOTERBEGIN, mb_substr($prettyLines[$line], 0, 1));
                        $this->assertEquals(Player::CHAR_FOOTEREND, mb_substr($prettyLines[$line], -1));
                        break;
                    default:
                        if ($line % 2) {
                            $beginChar = $endChar = Player::CHAR_PIPE;
                        } else {
                            $beginChar = Player::CHAR_ITEMBEGIN;
                            $endChar = Player::CHAR_ITEMEND;
                        }
                        $this->assertEquals($beginChar, mb_substr($prettyLines[$line], 0, 1));
                        $this->assertEquals($endChar, mb_substr($prettyLines[$line], -1));
                }
            }
        }
    }

    /**
     * Test exception when file doesn't exists
     */
    public function testInvalidFileFailure() {
        
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(10);
        $scoreBoard = new ScoreBoard(__DIR__ . Glossary::FAIL_FILES['INVALID_FILE']);
    } 

    /**
     * Test exception when file contains a row with invalid entry data
     * 
     * @covers Kriptosio\Bowling\Lib\Player
     * @covers Kriptosio\Bowling\Lib\Turn
     */
    public function testInvalidFormatFailure() {
        
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(20);
        $scoreBoard = new ScoreBoard(__DIR__ . Glossary::FAIL_FILES['INVALID_FORMAT']);
    } 

    /**
     * Test exception when file contains more than 10 turns
     * 
     * @covers Kriptosio\Bowling\Lib\Player
     * @covers Kriptosio\Bowling\Lib\Turn
     */
    public function testMoreThan10TurnsFailure() {
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(Turn::ERROR_MORE_THAN_10TURNS);
        $scoreBoard = new ScoreBoard(__DIR__ . Glossary::FAIL_FILES['MORE_THAN_10TURNS']);
    }

    /**
     * Test exception when file contains a invalid score at turn 10
     *
     * @covers Kriptosio\Bowling\Lib\Player
     * @covers Kriptosio\Bowling\Lib\Turn
     */
    public function testTurn10HasWrongDataFailure() {
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(Turn::ERROR_INVALIDEXTRASHOOT);
        $scoreBoard = new ScoreBoard(__DIR__ . Glossary::FAIL_FILES['WRONG_DATA']);
    }

    /**
     * Test exception when file contains a turn with more than 10 pines
     *      
     * @covers Kriptosio\Bowling\Lib\Player
     * @covers Kriptosio\Bowling\Lib\Turn
     */
    public function testTurn01HasMoreThan10pinesFailure() {
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(Turn::ERROR_MORE_THAN10POINTS);
        $scoreBoard = new ScoreBoard(__DIR__ . Glossary::FAIL_FILES['MORE_THAN_10PINES']);
    }
}
