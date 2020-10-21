<?php


namespace MazeKiller\core;


/**
 * Class Maze
 * States manager of the maze array,
 * Provides with getters, setters and checkers of different states
 *
 * @package MazeKiller\core
 */
class Maze implements MazeInterface
{
    private const START_POINT = 'startPoint';
    private const END_POINT = 'endPoint';
    private const DEAD_POINT = 'deadPoint';

    private array $mazeRaw;
    private array $initStates;
    private array $crossPoints = [];
    private array $maze;
    private int $nodesAmount;
    private int $colsNumber;
    private int $rowsNumber;
    private $startPoint;
    private array $endPoint;
    private array $newStates;


    public function __construct(array $maze)
    {
        $this->setData($maze);
    }

    /**
     * Set new maze data
     * @param  array  $maze
     */
    public function setData(array $maze): void
    {
        $this->setMaze($maze);
        $this->parseMaze();
    }

    /**
     * Initial parsing of maze, set basic data
     */
    private function parseMaze(): void
    {
        $rawMaze = $this->getMazeRaw();
        $this->colsNumber = count($rawMaze[0]);
        $this->rowsNumber = count($rawMaze);

        $this->nodesAmount = $this->colsNumber * $this->rowsNumber;

        $this->startPoint = '00';
        $this->endPoint = [$this->rowsNumber - 1, $this->colsNumber - 1];

        foreach ($rawMaze as $rowKey => $row) {
            foreach ($row as $colKey => $col) {
                $nodeValue = $col === '.' ? 0 : 1;
                $this->maze[$rowKey][$colKey] = $nodeValue;
            }
        }
        $this->setInitStates();
    }


    /**
     * @return mixed
     */
    public function getColsNumber(): int
    {
        return $this->colsNumber;
    }

    /**
     * @param  array  $maze
     */
    private function setMaze(array $maze): void
    {
        $this->mazeRaw = $maze;
    }

    /**
     * Check if node has only input and no allowed outputs,
     * except endPoint and startPoint
     *
     * @param  string  $location
     * @return bool
     */
    public function isDeadPoint(string $location): bool
    {
        $node = $this->getNodesPassStatesAround($location);

        $states = array_values($node['states']);

        return 3 <= array_sum($states)
            && !$this->isEndPoint($location) &&
            !$this->isStartPoint($location);
    }

    /**
     * Check if the location matches the end point of maze
     * @param  string  $location
     * @return bool
     */
    public function isEndPoint(string $location): bool
    {
        return $location === join('', $this->getEndPoint());
    }

    /**
     * The destination point in maze
     *
     * @return mixed
     */
    public function getEndPoint(): array
    {
        return $this->endPoint;
    }

    /**
     * The destination point in maze
     *
     * @return string
     */
    public function getEndPointLocation(): string
    {
        return join('', $this->endPoint);
    }

    /**
     * '00'
     * @return mixed
     */
    public function getStartPoint(): string
    {
        return $this->startPoint;
    }

    /**
     * @param  string  $location
     * @return bool
     */
    public function isStartPoint(string $location)
    {
        $startPoint = $this->getStartPoint();

        return $location === $startPoint;
    }


    /**
     * @param  string  $location
     * @param  array  $node  Optional, if added then read states from it
     * @return bool
     */
    public function isCrossPoint(string $location, $node = []): bool
    {
        $node = empty($node) ? $this->getNodesPassStatesAround($location) : $node;
        if ($this->isPassAbleNodeAt($location)) {
            $states = array_values($node['states']);
            if ($this->isStartPoint($location)) {
                return array_sum($states) <= 2;
            }

            return array_sum($states) < 2;
        }

        return false;
    }

    /**
     * Check if the location contains PassPoint, a node that has only 2 options
     * for changing state, where 1 is input, and 1 is output, i.a. only passing through is
     * available
     *
     * @param  string  $location
     * @return bool
     */
    public function isPassPoint(string $location): bool
    {
        $node = $this->getNodeStateAt($location);
        if ($node['isPassAble']) {
            $states = array_values($node['states']);

            return 2 === array_sum($states);
        }

        return false;
    }

    /**
     * Retrieve  nodes' states of maze array, where:
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
     *      'isPassAble' =>true
     *  ],
     *  ...
     * ]
     *
     * @return array
     */
    public function getInitStatesMap(): array
    {
        return $this->initStates;
    }

    /**
     * Defines initial states of nodes in maze
     * @return void
     */
    private function setInitStates()
    {
        $initStates = [];
        $maze = $this->maze;

        foreach ($maze as $rowNum => $colsArray) {
            foreach ($colsArray as $colNum => $pointState) {
                $location = $rowNum.$colNum;
                $initStates[$location] = $this->getNodeStateAt($location);

                if ($this->isCrossPoint($location)) {
                    $this->setCrossPoint($location);
                }
            }
        }

        $this->initStates = $initStates;
    }

    /**
     * Define location of the node by given current location and direction to test
     *
     * @param  string  $location
     * @param  string  $direction
     * @return string
     */
    public function getNodeLocationFromTo(string $location, string $direction): string
    {
        $row = (int)$location[0];
        $col = (int)$location[1];

        $newLocation = '';

        switch ($direction) {
            case 'up':
                $newLocation = ($row - 1).$col;
                break;
            case 'down':
                $newLocation = ($row + 1).$col;
                break;
            case 'left':
                $newLocation = $row.($col - 1);
                break;
            case 'right':
                $newLocation = $row.($col + 1);
                break;
        }

        return $newLocation;
    }

    /**
     * Gets states (wall or not) of nodes around the specific location
     *
     * @param  string  $location
     * @return array
     */
    public function getNodesPassStatesAround(string $location): array
    {
        $row = (int)$location[0];
        $col = (int)$location[1];

        if (!$this->isNotWallAt($location)) {
            return [
                'states' => [
                    'up' => 1,
                    'down' => 1,
                    'left' => 1,
                    'right' => 1,
                ],
            ];
        }
        $lastRowIndex = $this->getRowsNumber() - 1;
        $lastColIndex = $this->getColsNumber() - 1;

        $maze = $this->maze;
        switch ($row) {
            case 0:

                $states['up'] = 1;//out of the top edge
                $states['down'] = $maze[$row + 1][$col];
                break;
            case $lastRowIndex:
            case $lastRowIndex + 1:
                $states['up'] = $maze[$row - 1][$col];
                $states['down'] = 1;//below the bottom edge
                break;
            default:
                $states['up'] = $maze[$row - 1][$col];
                $states['down'] = $maze[$row + 1][$col];
                break;
        }

        switch ($col) {
            case 0:
                $states['left'] = 1;//out of the left edge
                $states['right'] = $maze[$row][$col + 1];
                break;
            case $lastColIndex:
            case $lastColIndex + 1 :
                $states['right'] = 1;//out of the right edge
                $states['left'] = $maze[$row][$col - 1];
                break;
            default:

                $states['left'] = $maze[$row][$col - 1];
                $states['right'] = $maze[$row][$col + 1];
                break;
        }

        return [
            'states' => $states,
        ];
    }

    /**
     * @param  string  $location
     * @param  string  $direction
     * @return bool
     */
    public function isSafeDirectionFromTo(string $location, string $direction)
    {
        $testNodeLocation = $this->getNodeLocationFromTo($location, $direction);

        return $this->isPassAbleNodeAt($testNodeLocation) || $this->isEndPoint($testNodeLocation);
    }

    /**
     * @param  string  $location
     * @return array
     */
    public function getAvailableOutsFrom(string $location): array
    {
        $node = $this->getNodesPassStatesAround($location);
        if ($this->isEndPoint($location)
            || $this->isDeadPoint($location)) {
            return [];
        }
        $availableOuts = array_filter(
            $node['states'],
            function ($state, $direction) use ($location) {
                return $state === 0 && $this->isSafeDirectionFromTo($location, $direction);
            },
            ARRAY_FILTER_USE_BOTH
        );
        $availableOuts = array_keys($availableOuts);

        return $availableOuts;
    }

    /**
     * Test neighbor nodes for their types
     * @param  string  $location
     * @param  string  $nodeType
     * @return bool
     */
    public function isNearToNodeFrom(string $location, string $nodeType): bool
    {
        $node = $this->getNodesPassStatesAround($location);

        $availableOuts = array_keys(
            array_filter(
                $node['states'],
                function ($state) {
                    return $state === 0;
                }
            )
        );

        foreach ($availableOuts as $out) {
            $testedLocation = $this->getNodeLocationFromTo($location, $out);
            switch ($nodeType) {
                case self::START_POINT:
                    if ($this->isStartPoint($testedLocation)) {
                        return true;
                    }
                    break;
                case self::END_POINT:
                    if ($this->isEndPoint($testedLocation)) {
                        return true;
                    }
                    break;
                case self::DEAD_POINT:
                    if ($this->isDeadPoint($testedLocation)) {
                        return true;
                    }
                    break;
            }
        }

        return false;
    }

    /**
     * Get directions to a required node from a specific location
     *
     * @param  string  $location
     * @param  string  $nodeType
     * @return array  directions like 'left', 'right' 'up' 'down'
     */
    public function getDirectionsToNodeFrom(string $location, string $nodeType): array
    {
        $node = $this->getNodesPassStatesAround($location);
        if (!$this->isPassAbleNodeAt($location)
            && !$this->isStartPoint($location)
            && !$this->isEndPoint($location)) {
            return [];
        }

        $availableOuts = array_keys(
            array_filter(
                $node['states'],
                function ($state) {
                    return $state === 0;
                }
            )
        );

        $directions = [];
        foreach ($availableOuts as $out) {
            $testedLocation = $this->getNodeLocationFromTo($location, $out);
            switch ($nodeType) {
                case self::START_POINT:
                    if ($this->isStartPoint($testedLocation)) {
                        $directions[] = $out;
                    }
                    break;
                case self::END_POINT:
                    if ($this->isEndPoint($testedLocation)) {
                        $directions[] = $out;
                    }
                    break;
                case self::DEAD_POINT:
                    if ($this->isDeadPoint($testedLocation)) {
                        $directions[] = $out;
                    }
                    break;
            }
        }

        return $directions;
    }

    /**
     * Check and return all states for a node on a specific location in maze
     * @param  string  $location
     * @return array
     */
    public function getNodeStateAt(string $location): array
    {
        $node = $this->getNodesPassStatesAround($location);

        $isPassAbleNode = $this->isPassAbleNodeAt($location);
        $isEndPoint = $this->isEndPoint($location);
        $isNearStartPoint = $this->isNearToNodeFrom($location, self::START_POINT);

        $endPointDirection = $this->getDirectionsToNodeFrom($location, self::END_POINT);
        $isNearEndPoint = $this->isNearToNodeFrom($location, self::END_POINT);
        $isDeadPoint = $this->isDeadPoint($location);

        $availableAndSafeOuts = $this->getAvailableOutsFrom($location);
        $forcedOut = [];
        if (count($availableAndSafeOuts) === 1 && !$isEndPoint && !$isDeadPoint) {
            $forcedOut = $availableAndSafeOuts;
        }

        return [
            'states' => $node['states'],
            'availableOuts' => $this->isNotWallAt($location) ? $this->getAvailableOutsFrom($location) : [],
            'forcedOut' => $this->isNotWallAt($location) ? $forcedOut : [],
            'isPassAble' => $isPassAbleNode,
            'isPassed' => false,
            'isCrossPoint' => $this->isCrossPoint($location),
            'isStartPoint' => $this->isStartPoint($location),
            'isNearStartPoint' => $isNearStartPoint,
            'nearStartPointOn' => $isNearStartPoint ? $this->getDirectionsToNodeFrom($location, self::START_POINT) : [],
            'isEndPoint' => $isEndPoint,
            'isNearEndPoint' => $isNearEndPoint,
            'nearEndPointOn' => $endPointDirection,
            'isDeadPoint' => $isPassAbleNode ? $isDeadPoint : true,
            'nearDeadPointsOn' => $isDeadPoint ? [] : $this->getDirectionsToNodeFrom($location, self::DEAD_POINT),
        ];
    }

    /**
     * Number of rows in maze
     *
     * @return int
     */
    public function getRowsNumber(): int
    {
        return $this->rowsNumber;
    }

    /**
     * Check if node is available (equals 0) on the location
     *
     * @param  string  $location
     * @return bool
     */
    public function isPassAbleNodeAt(string $location): bool
    {
        if ($this->isStartPoint($location)
            || $this->isEndPoint($location)
            || $this->isDeadPoint($location)) {
            return false;
        }

        return $this->isNotWallAt($location);
    }

    /**
     * @param  string  $location
     * @return bool
     */
    private function isNotWallAt(string $location)
    {
        $row = (int)$location[0];
        $col = (int)$location[1];

        $maze = $this->maze;

        return $maze[$row][$col] === 0;
    }

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
     * @return array of nodes with all states
     */
    public function getCrossPoints(): array
    {
        return $this->crossPoints;
    }

    /**
     * @return array of node locatons
     */
    public function getCrossPointsLocations(): array
    {
        return array_keys($this->crossPoints);
    }

    /**
     * @param  string  $location
     */
    private function setCrossPoint(string $location): void
    {
        $node = $this->getNodeStateAt($location);
        $this->crossPoints[$location] = $node;
    }

    /**
     * @return int
     */
    public function getNodesAmount(): int
    {
        return $this->nodesAmount;
    }

    /**
     * @param  array  $states
     */
    public function setNewStates(array $states): void
    {
        $this->newStates = $states;
    }

    /**
     * @return array
     */
    public function getNewStates(): array
    {
        return $this->newStates;
    }

    /**
     * Reset nodes for next iteration with forced outputs in cross points
     * @param  array  $initCrossPoints
     * @param  array  $crossPointsForcedOutputs
     * @return mixed|void
     */
    public function resetNewStatesExcept(array $initCrossPoints = [], array $crossPointsForcedOutputs = [])
    {
        $initStates = $this->getInitStatesMap();
        $newStates = $this->getNewStates();

        if (!empty($initCrossPoints)) {
            foreach ($newStates as $location => $node) {
                $newStates[$location] = $initStates[$location];
                if (in_array($location, array_keys($crossPointsForcedOutputs), true)) {
                    $newStates[$location]['forcedOut'] = [$crossPointsForcedOutputs[$location]];
                }
            }
            $this->setNewStates($newStates);
        } else {
            $this->setNewStates($initStates);
        }
    }

    /**
     * Retrieves CrossPoints from preset nodes array
     * @param  array  $nodes
     * @return
     *      */
    public function getCrossPointsFrom(array $nodes = []): array
    {
        $nodes = empty($nodes) ? $this->getCrossPoints() : $nodes;
        $crossPoints = [];

        foreach ($nodes as $location => $node) {
            if ($this->isCrossPoint($location, $node)) {
                $crossPoints[] = $location;
            }
        }

        return $crossPoints;
    }

    /**
     * Get maze raw data, as it was set by user ( with '.', '#')
     * @return array
     */
    public function getMazeRaw(): array
    {
        return $this->mazeRaw;
    }
}