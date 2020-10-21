<?php

namespace MazeKiller\view;


class Home
{
    public function index(array $context = [])
    {
        $context['title'] = $context['title'] ?? 'Maze Solver';
        include_once 'templates/home.php';
    }


}