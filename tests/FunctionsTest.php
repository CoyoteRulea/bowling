<?php
namespace Kriptosio\Bowling;

use PHPUnit\Framework\TestCase;
use Kriptosio\Bowling\Lib\Functions;
use Kriptosio\Bowling\Lib\Glossary;

/**
 * @covers Kriptosio\Bowling\Lib\Functions
 */
final class FunctionsTest extends TestCase
{
    public function testGetColumFromCSVFailure() {
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(sprintf(Glossary::ERROR_UNABLE_TO_OPEN_FILE, __DIR__ . Glossary::FAIL_FILES['INVALID_FILE']));
        $playersCSV = Functions::getColumFromCSV(__DIR__ . Glossary::FAIL_FILES['INVALID_FILE'], 0, true);
    }
}
