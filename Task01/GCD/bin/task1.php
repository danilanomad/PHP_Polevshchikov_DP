#!/usr/bin/env php
<?php

$loadPath1 = __DIR__ . '/../../../autoload.php';
$loadPath2 = __DIR__ . '/../vendor/autoload.php';

if (file_exists($loadPath1)) {
    require_once $loadPath1;
} else {
    require_once $loadPath2;
}
use danilanomad\GCD\Controller;

$controller = new danilanomad\GCD\Controller();
$controller->startGame();