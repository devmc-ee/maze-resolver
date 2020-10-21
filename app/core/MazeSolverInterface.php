<?php


namespace MazeKiller\core;


/**
 * Interface MazeSolverInterface
 *
 * @package MazeKiller\core
 */
interface MazeSolverInterface
{
    /**
     * Finds the shortest path, that has less steps to resolve the maze
     * @return int
     */
    public function getShortestRouteStepsAmount(): int;

    public function getRoutes(): array;

    /**
     * Filters states array and returns keys of the allowed directions
     *
     * @param  array  $states  node states,
     * @return array available directions like ['left', 'down']
     */
    public function getAllowedDirectionsFrom(array $states): array;

    public function getRoutesOptionsFrom(array $crossPoints): array;

    public function getBetterDirection(array $directions, string $currentLocation, array $nodes): string;

    public function getChangedStatesOnStepFor(string $currentLocation, string $direction, array $states);

    public function getRouteFrom(array $states): array;

    public function getCrossPoints(): array;

    public function getStates(): array;
}