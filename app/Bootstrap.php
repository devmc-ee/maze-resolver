<?php


namespace MazeKiller;

use MazeKiller\view\Home;

/**
 * Class Bootstrap
 * @package MazeKiller
 */
class Bootstrap
{
    public function __construct()
    {
        $homePage = new Home();
        $context['Title'] = 'Maze Walker';
        $homePage->index($context);
    }


}