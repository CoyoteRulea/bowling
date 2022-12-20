<?php

namespace Kriptosio\Bowling\Lib;

use Kriptosio\Bowling\Lib\Glossary;
use Exception;

class Functions {
    /**
     * Array with column in a CSV File
     * 
     * @param string $filename CSV file to be readed
     * @param int $column Column to be returned
     * @param bool $unique (optional) if true returns only not repeated values
     * @param bool $excludeHeader (optional) if true excludes first line on file otherwise first line included
     * 
     * @return array array with values at specific column
     */
    public static function getColumFromCSV(string $filename, int $column, bool $unique = false, bool $excludeHeader = false) : array {
        $column = [];

        if (($file = @fopen($filename, "r")) !== FALSE) {
            while (($data = fgetcsv($file, 1000, " ")) !== FALSE) {

                if ($excludeHeader) continue;
                
                if ($unique && in_array($data[0], $column, true)) continue;

                $column[] = $data[0];
            }
            fclose($file);
        } else {
            // If one line contains more elements than expected
            throw new Exception(sprintf(Glossary::ERROR_UNABLE_TO_OPEN_FILE, $filename));
        }

        return $column;
    }
}
