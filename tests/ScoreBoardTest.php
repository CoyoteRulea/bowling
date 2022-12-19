<?php
namespace Kriptosio\Bowling;

use PHPUnit\Framework\TestCase;
use Kriptosio\Bowling\Lib\Functions;
use Kriptosio\Bowling\Lib\ScoreBoard;
use Kriptosio\Bowling\Lib\Player;

final class ScoreBoardTest extends TestCase
{
    const FAIL_FILES = [
        'INVALID_FILE' => '\assets\fail-thisfiledoesntexists.txt',
        'INVALID_FORMAT' => '\assets\fail-invalidformatatline12.txt',
        'MORE_THAN_10TURNS' => '\assets\fail-morethan10turns.txt',
        'MORE_THAN_10PINES' => '\assets\fail-turn01HasMoreThan10pines.txt',
        'WRONG_DATA' => '\assets\fail-turn10haswrongdata.txt'
    ];

    const SUCCESS_FILES = [
        'ONE_PLAYER' => '\assets\success-oneplayer.txt',
        'ONE_PLAYER_PERFECT_MATCH' => '\assets\success-oneplayerperfectmatch.txt',
        'TWO_PLAYERS' => '\assets\success-twoplayers.txt',
        'TWO_PLAYERS_PERFECT_MATCH' => '\assets\success-twoplayersperfectmatch.txt',
        'THREE_PLAYERS' => '\assets\success-threeplayers.txt'
    ];
    
    /**
     * Test a valid score boards files
     */
    public function testScoreBoardSuccess() {

        foreach (self::SUCCESS_FILES as $key => $file) {
            $filewithpath = __DIR__ . $file;

            // Initialize test variables
            $scoreBoard = new ScoreBoard($filewithpath);
            $playersNumber = $scoreBoard->getPlayersCount();
            $playersObj = $scoreBoard->getPlayers();
            $playersCSV = Functions::getColumFromCSV($filewithpath, 0, true);

            $this->assertIsArray($playersCSV);
            $this->assertEquals(count($playersCSV), $playersNumber);

            for ($item = 0; $item < $playersNumber; $item++) {
                $this->assertArrayHasKey($playersCSV[$item], $playersObj);
            }
        }

        $this->assertInstanceOf(ScoreBoard::class, $scoreBoard);
    }

    /**
     * Test exception when file doesn't exists
     */
    public function testInvalidFileFailure() {
        
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(10);
        $scoreBoard = new ScoreBoard(__DIR__ . self::FAIL_FILES['INVALID_FILE']);
    } 

    /**
     * Test exception when file contains a row with invalid entry data
     */
    public function testInvalidFormatFailure() {
        
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(20);
        $scoreBoard = new ScoreBoard(__DIR__ . self::FAIL_FILES['INVALID_FORMAT']);
    } 

    /**
     * Test exception when file contains more than 10 turns
     */
    public function testMoreThan10TurnsFailure() {
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ERROR: An Unexpected Error Ocurred. Score file contains more than 10 turns.');
        $scoreBoard = new ScoreBoard(__DIR__ . self::FAIL_FILES['MORE_THAN_10TURNS']);
    }

    /**
     * Test exception when file contains a invalid score at turn 10
     */
    public function testTurn10HasWrongDataFailure() {
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ERROR: An Unexpected Error Ocurred. Turn 10 only can have a extra shoot if first shoot in turn 10 is strike.');
        $scoreBoard = new ScoreBoard(__DIR__ . self::FAIL_FILES['WRONG_DATA']);
    }

    /**
     * Test exception when file contains a turn with more than 10 pines
     */
    public function testTurn01HasMoreThan10pinesFailure() {
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ERROR: An Unexpected Error Ocurred. Lines can\'t have more than 10 pines per turn.');
        $scoreBoard = new ScoreBoard(__DIR__ . self::FAIL_FILES['MORE_THAN_10PINES']);
    }
}
