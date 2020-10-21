<?php


namespace MazeKiller\core;


interface MazeInterface
{
    /**
     * Parse input array,
     * @param  array  $maze
     * @return mixed
     */
    public function setData(array $maze): void;

    public function setNewStates(array $states): void;

    public function getNewStates(): array;

    /**
     * @param  array  $initCrossPoints
     * @param  array  $crossPointsForcedOutputs
     * @return mixed
     */
    public function resetNewStatesExcept(array $initCrossPoints = [], array $crossPointsForcedOutputs = []);

    public function getNodeLocationFromTo(string $location, string $direction): string;

    public function getCrossPointsFrom(array $states): array;

    public function getCrossPointsLocations(): array;

    public function getEndPointLocation(): string;

    /**
     * Returns maze columns amount number
     * @return int
     */
    public function getColsNumber(): int;

    public function getRowsNumber(): int;

    public function getNodesAmount(): int;

    public function getStartPoint(): string;

    public function getEndPoint(): array;

    /**
     * Check if point has only input and no allowed outputs
     *
     * @param  string  $location
     * @return bool
     */
    public function isDeadPoint(string $location): bool;

    public function isCrossPoint(string $location): bool;

    /**
     * Check if the location contains PassPoint, a node that has only 2 options
     * for changing state, where 1 is input, and 1 is output, i.a. only passing through is
     * available
     *
     * @param  string  $location
     * @return bool
     */
    public function isPassPoint(string $location): bool;

    /**
     * Check if the location matches the end point of maze
     * @param  string  $location
     * @return bool
     */
    public function isEndPoint(string $location): bool;

    /**
     * Nodes' states of maze array, where:
     * - key is a location of the central node that is tested, where location is:
     *   rowNum-1.colNum-1
     * - value is a States array representing storing states of the neighbor nodes;
     *  The value is representation of 'Top Bottom Left Right' nodes
     *  where:
     *      0 = available for move/change state,
     *      1 = not available [full/ locked/ a wall/used before];
     *
     * For example, if the maze array is:
     *   ['.', '.'],
     *   ['#', '.'],
     * returned array might be (row.col => ['TopBottomLeftRight']):
     *
     *  [
     *   '00' => [
     *      'states'=>[
     *          'up' => 0,
     *          'down' => 0,
     *          'left' => 1,
     *          'right' => 1,
     *      ],
     *      'isAvailable' =>true
     *  ],
     *  ...
     * ]
     *
     * @return array
     */
    public function getInitStatesMap(): array;

    /**
     * Return array of node states and status
     * [
     *      'states' => [
     *          'up' => 1|0,
     *          'down' => 1|0,
     *          'left' => 1|0,
     *          'right' => 1|0,
     *       ],
     *      'isAvailable' => true|false,
     * ]
     * @param  string  $location
     * @return array
     */
    public function getNodeStateAt(string $location): array;

    public function getNodesPassStatesAround(string $location): array;

    /**
     * Check if node  is available (equals 0) on the location
     *
     * @param  string  $location
     * @return bool
     */
    public function isPassAbleNodeAt(string $location): bool;

    /**
     * Get array of cross-points, the nodes that have more than 1 available option to
     * make next move (change state from 0 to 1)
     *
     * For example, if the maze array is:
     *   [
     *      ['.', '.', '.'],
     *      ['#', '.', '.'],
     * ]
     *
     * the cross point is at '01':
     * [ '01' =>[
     *      states => [
     *          'up' => '1',
     *          'down' => '0', // first option,
     *          'left'  => '0',  // could be an option, but in this array it is an input
     *          'right' => '0'   // second option
     *     ],
     * ]
     * ...
     * ]
     * @return array
     */
    public function getCrossPoints(): array;

}