<?php

use MazeKiller\core\Maze;
use MazeKiller\core\MazeSolver;

require '../vendor/autoload.php';



if(empty($_POST['maze'])){
    exit(json_encode(['error'=>'Something went wrong. Please make sure that all required fields are filled.',
        'request_data'=>$_POST]));
}

$mazeArray = json_decode($_POST['maze']);

$maze = new Maze($mazeArray);
try{
    $mazeSolver = new MazeSolver($maze);
    echo json_encode(["routes"=>$mazeSolver->getOptimalRoutes()]);
}catch (IOException $e){
    echo json_encode(["error"=>$e->getMessage()]);
}


die();