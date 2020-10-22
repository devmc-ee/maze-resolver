<?php

use MazeKiller\core\Maze;
use MazeKiller\core\MazeSolver;

require '../vendor/autoload.php';

header('Content-Type: application/json');

if (empty($_POST['maze'])) {
    exit(
    json_encode(
        [
            'error' => 'Something went wrong. Please make sure that all required fields are filled.',
            'request_data' => $_POST,
        ]
    )
    );
}

$mazeArray = json_decode($_POST['maze']);

$maze = new Maze($mazeArray);
$routes = [];
try {
    $mazeSolver = new MazeSolver($maze);
    $routes = $mazeSolver->getOptimalRoutes();
} catch (Exception $e) {

    echo json_encode(["error" => $e->getMessage()]);
    exit;
}
echo json_encode(["routes" => $routes]);

die();