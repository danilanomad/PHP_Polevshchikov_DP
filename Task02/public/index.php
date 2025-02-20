<?php
session_start();
require_once __DIR__ . '/../src/controllers/HomeController.php';
require_once __DIR__ . '/../src/controllers/GameController.php';
require_once __DIR__ . '/../src/controllers/ResultsController.php';
require_once __DIR__ . '/../src/Model.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'game':
        $controller = new \danilanomad\GCD\Controllers\GameController();
        break;
    case 'results':
        $controller = new \danilanomad\GCD\Controllers\ResultsController();
        break;
    case 'home':
    default:
        $controller = new \danilanomad\GCD\Controllers\HomeController();
}

$controller->handleRequest();
