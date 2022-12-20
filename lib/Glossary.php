<?php

namespace Kriptosio\Bowling\Lib;


class Glossary {
    /**
     * PHP Unit Test Files for test purposes
     */
    const FAIL_FILES = [
        'INVALID_FILE' => '/assets/fail-thisfiledoesntexists.txt',
        'INVALID_FORMAT' => '/assets/fail-invalidformatatline12.txt',
        'MORE_THAN_10TURNS' => '/assets/fail-morethan10turns.txt',
        'MORE_THAN_10PINES' => '/assets/fail-turn01HasMoreThan10pines.txt',
        'WRONG_DATA' => '/assets/fail-turn10haswrongdata.txt'
    ];

    const SUCCESS_FILES = [
        'ONE_PLAYER' => '/assets/success-oneplayer.txt',
        'ONE_PLAYER_PERFECT_MATCH' => '/assets/success-oneplayerperfectmatch.txt',
        'TWO_PLAYERS' => '/assets/success-twoplayers.txt',
        'TWO_PLAYERS_PERFECT_MATCH' => '/assets/success-twoplayersperfectmatch.txt',
        'THREE_PLAYERS' => '/assets/success-threeplayers.txt'
    ];

    /**
     * Thrown General Exceptions
     */
    const ERROR_UNABLE_TO_OPEN_FILE  = "ERROR: Unable to load file %s";
    const ERROR_REQUIRED_FORMAT_FAIL = "ERROR: Required format fail on %s at line %d.";
}