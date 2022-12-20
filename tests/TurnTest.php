<?php
namespace Kriptosio\Bowling;

use PHPUnit\Framework\TestCase;
use Kriptosio\Bowling\Lib\Functions;
use Kriptosio\Bowling\Lib\Turn;

/**
 * @covers Kriptosio\Bowling\Lib\Turn
 */
final class TurnTest extends TestCase
{
    /**
     * Test Turn construction
     */
    public function testCreateAllValidTurnsSuccess() {

        // Test valid response creating turn from 
        for ($turn = 1; $turn <= 10; $turn++) {
            // Element created correctly
            $turnObj = new Turn($turn);
            $this->assertInstanceOf('Kriptosio\Bowling\Lib\Turn', $turnObj);

            // Validate toString value
            $this->assertEquals($turn != 10 ? " \t\t" : "\t\t ", (string) $turnObj);
        }
    }

    /**
     * Test Turn construction
     */
    public function testCreateTurnFailure() {

        // Negative turn
        $invalidTurn = -1;
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(Turn::ERROR_INVALID_10TH_TURN);
        $this->expectExceptionCode($invalidTurn);
        $turn = new Turn($invalidTurn);

        // Zero turn
        $invalidTurn = 0;
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(Turn::ERROR_INVALID_10TH_TURN);
        $this->expectExceptionCode($invalidTurn);
        $turn = new Turn($invalidTurn);

        // Turn after 10
        $invalidTurn = 11;
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(Turn::ERROR_INVALID_10TH_TURN);
        $this->expectExceptionCode($invalidTurn);
        $turn = new Turn($invalidTurn);
    }

    /**
     * Test to validate states on normal turn with pines below 10 falls
     */
    public function testAddScoresToTurn() {

        // Element created correctly
        $turn = mt_rand(1, 9);
        $turnObj = new Turn($turn);
        $this->assertInstanceOf('Kriptosio\Bowling\Lib\Turn', $turnObj);
        $this->assertEquals($turnObj->getTurnNumber(), $turn);

        // assign and validate left value
        $left = mt_rand(0, 9);
        $turnObj->addNextScore($left);
        $this->assertEquals("$left\t\t", (string) $turnObj);
        $this->assertNull($turnObj->getNext());

        // Assign and validate right value
        $right = mt_rand(0, 9 - $left);
        $turnObj->addNextScore($right);
        $this->assertEquals("$left\t$right\t", (string) $turnObj);
        $nextObj = $turnObj->getNext();
        $this->assertNotNull($nextObj);
        $this->assertEquals($nextObj->getTurnNumber(), $turn + 1);

        // Validate Score
        $this->assertEquals($left + $right, $turnObj->getScore());
    }
    /**
     * Test to validate states on 10th turn with pines below 10 falls
     */
    public function testAddScoresTo10thTurn() {

        // Element created correctly
        $turn = 10;
        $turnObj = new Turn($turn);
        $this->assertInstanceOf('Kriptosio\Bowling\Lib\Turn', $turnObj);
        $this->assertEquals($turnObj->getTurnNumber(), $turn);

        // assign and validate left value
        $left = mt_rand(0, 9);
        $turnObj->addNextScore($left);
        $this->assertEquals("$left\t\t ", (string) $turnObj);
        $this->assertNull($turnObj->getNext());

        // Assign and validate right value
        $right = mt_rand(0, 9 - $left);
        $turnObj->addNextScore($right);
        $this->assertEquals("$left\t$right\t ", (string) $turnObj);
        $this->assertNull($turnObj->getNext());

        // Validate Score
        $this->assertEquals($left + $right, $turnObj->getScore());  

        // Try to assign invalid third value 
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(Turn::ERROR_INVALIDEXTRASHOOT);
        $extra = mt_rand(0, 10);
        $turnObj->addNextScore($extra);
    }

    /**
     * Test to validate states where extra turn applies
     */
    public function testAddScoresTo10thExtraTurn() {

        // Element created correctly
        $turn = 10;
        $turnObj = new Turn($turn);
        $this->assertInstanceOf('Kriptosio\Bowling\Lib\Turn', $turnObj);
        $this->assertEquals($turnObj->getTurnNumber(), $turn);

        // assign and validate left value
        $turnObj->addNextScore(10);
        $this->assertEquals("X\t\t ", (string) $turnObj);
        $this->assertNull($turnObj->getNext());

        // Assign and validate right value
        $right = mt_rand(0, 9);
        $turnObj->addNextScore($right);
        $this->assertEquals("X\t$right\t ", (string) $turnObj);
        $this->assertNull($turnObj->getNext());

        // Assign third value 
        $extra = mt_rand(0, 9 - $right);
        $turnObj->addNextScore($extra);
        $this->assertEquals("X\t$right\t$extra", (string) $turnObj);
        $this->assertNull($turnObj->getNext());

        // Validate Score
        $this->assertEquals(10 + $right + $extra, $turnObj->getScore()); 
    }

    /**
     * Test to validate scores on normal turn when strike
     */
    public function testStrikeScore() {
        // Element created correctly
        $turn = mt_rand(1, 9);
        $turnObj = new Turn($turn);
        $this->assertInstanceOf('Kriptosio\Bowling\Lib\Turn', $turnObj);
        $this->assertEquals($turnObj->getTurnNumber(), $turn);

        // assign and validate left value
        $turnObj->addNextScore(10);
        $this->assertEquals(" \tX\t", (string) $turnObj);
        $nextObj = $turnObj->getNext();
        $this->assertNotNull($nextObj);

        // Assign next values
        $left = mt_rand(0, 10);
        $nextObj->addNextScore($left);
        $right = mt_rand(0, 10 - $left);
        $nextObj->addNextScore($right);

        // To current score add next two points
        $this->assertEquals(10 + $left + $right, $turnObj->getScore());
    }

    /**
     * Test to validate scores on normal turn when spare
     */
    public function testSpareScore() {
        // Element created correctly
        $turn = mt_rand(1, 9);
        $turnObj = new Turn($turn);
        $this->assertInstanceOf('Kriptosio\Bowling\Lib\Turn', $turnObj);
        $this->assertEquals($turnObj->getTurnNumber(), $turn);

        // assign and validate left value
        $left = mt_rand(0, 9);
        $turnObj->addNextScore($left);
        $this->assertEquals("$left\t\t", (string) $turnObj);
        $this->assertNull($turnObj->getNext());

        // assign and validate right 
        $right = 10 - $left;
        $turnObj->addNextScore($right);
        $this->assertEquals("$left\t/\t", (string) $turnObj);
        $nextObj = $turnObj->getNext();
        $this->assertNotNull($nextObj);

        // Assign next values
        $nleft = mt_rand(0, 9);
        $nextObj->addNextScore($nleft);
        $nright = mt_rand(0, 9 - $nleft);
        $nextObj->addNextScore($nright);

        // To current score add next point only
        $this->assertEquals(10 + $nleft, $turnObj->getScore());
    }

    /**
     * Test to validate scores on normal turn when strike
     */
    public function testStrike10thScore() {
        // Element created correctly
        $turn = 10;
        $turnObj = new Turn($turn);
        $this->assertInstanceOf('Kriptosio\Bowling\Lib\Turn', $turnObj);
        $this->assertEquals($turnObj->getTurnNumber(), $turn);

        // assign and validate left value
        $turnObj->addNextScore(10);
        $this->assertEquals("X\t\t ", (string) $turnObj);
        $this->assertNull($turnObj->getNext());

        // Assign next values
        $left = mt_rand(0, 9);
        $turnObj->addNextScore($left);
        $this->assertNull($turnObj->getNext());
        $right = mt_rand(0, 9 - $left);
        $turnObj->addNextScore($right);
        $this->assertNull($turnObj->getNext());

        // To current score add next two points
        $this->assertEquals(10 + $left + $right, $turnObj->getScore());
    }
}
