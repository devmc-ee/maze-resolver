# 2D Maze Resolver

A simple app for finding the minimal required steps to walk through a maze, and shows the optimal routes on the maze.
Built with php (7.4+) and Vanilla js.

The idea is to find and show the optimal paths from top left corner of the maze to the right bottom corner.

## How to use

Select number of rows and columns of maze

Add some walls by clicking on the cells

## How it works
User creates a maze which is a 2D array that consists of empty paths (represented by '.') and walls (represented by '#'). 
User selects the location of the walls and submits the result.

There 2 classes: Maze and MazeSolver. 

Maze class is a kind of states manager, that converts values of the received maze array into 1 and 0 (walls and paths correspondingly), 
optimizes paths on the node level ( if any node can be passed around without changing the number of steps it becomes a wall (cannot pass through it)).
provides methods for working with states (add/change/delete/test)

MazeSolver receives an instance of the Maze class. This class works with the states of the maze object.
It generates all possible routes options and tests them using states of the maze nodes. The routes with the minimal steps
amount are saved and returned to the frontend.

## Install

Clone the repository
````
$ git clone https://github.com/devmc-ee/maze-resolver.git
````

Install composer:
for development (installs autoloader + phpunit)
````
$ composer install
````

for deploy:
````
$ composer install --no-dev --optimize-autoloader
````

PS. if composer is not installer globally, then use composer.phar
````
$ php composer.phar [command --options]
````