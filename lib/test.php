<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Kriptosio\Bowling\Lib\ScoreBoard;

// Code goes here \/\/\
$turn = new ScoreBoard('scores.txt');

echo (string) $turn;
