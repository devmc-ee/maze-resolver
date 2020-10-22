# 2D Maze Resolver

Simple app for finding the minimal required steps to walk through a maze.

Built with php (7.4+) and Vanilla js.

## How to use
Select number of rows and columns of maze

Add some walls by clicking on the cells

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