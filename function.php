<?php

use MazeKiller\core\Maze;
use MazeKiller\core\MazeSolver;

/**
 * Returns minimum possible number of steps between start ($maze[0][0]) and end ($maze[n-1][m-1]).
 *
 * @param array $maze multi-dimensional array
 * @return integer amount of steps to pass the shortest way from top-left corner to the bottom-right corner
 * @throws Exception if $maze argument is invalid or maze cannot be passed
 */
function findShortestPath(array $maze)
{
   $mazeSolver = new MazeSolver(new Maze($maze));

    return $mazeSolver->getShortestRouteStepsAmount();
}
