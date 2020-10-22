<?php


namespace MazeKiller\core;

/**
 * Class MazeSolver
 * Defines the optimal routes for solving mazes
 *
 *
 * @package MazeKiller\core
 */
class MazeSolver implements MazeSolverInterface
{
    private MazeInterface $maze;

    private array $routes; //generated Routes
    private array $crossPointsOutOptions;
    private array $optimalRoutes;

    public function __construct(MazeInterface $maze)
    {


        $this->maze = $maze;
        $this->setRoutes();
    }

    /**
     * Catches the moment when fatal error occurred and sends message in JSON,
     * thus the frontend can handle it and display to user
     */
    private function registerShutDownFunction()
    {
        ini_set('display_errors', false);
        register_shutdown_function(
            function () {
                $error = error_get_last();
                if (null !== $error) {
                    echo json_encode(
                        [
                            'error' => "Too many possible combinations of different routes!! Please add some more walls to reduce 
                            the number of combinations to analyze.",
                            'message' =>$error['message']
                        ]
                    );
                }
            }
        );
    }
    /**
     * Finds the shortest path, that has less steps to resolve the maze
     * @return int
     */
    public function getShortestRouteStepsAmount(): int
    {
        $optimalRoutes = $this->getOptimalRoutes();

        if (empty($optimalRoutes)) {
            return 0;
        }

        return count($optimalRoutes[0]);
    }


    /**
     * Maximum number of possible steps
     * @return int
     */
    private function getMaxStepsAmount(): int
    {
        return $this->maze->getNodesAmount();
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param  array  $nodes
     * @return array
     */
    public function getRouteFrom(array $nodes): array
    {
        $route = [];

        $currentLocation = $this->maze->getStartPoint();
        $endPoint = $this->maze->getEndPointLocation();
        $maxStepsAmount = $this->getMaxStepsAmount();

        $stepsCounter = 0; //protection against infinite loops

        while ($currentLocation !== $endPoint
            && $stepsCounter < $maxStepsAmount) {
            $node = $nodes[$currentLocation];

            $directions = empty($node['forcedOut']) ? $node['availableOuts'] : $node['forcedOut'];

            if ($node['isPassed']
                || $node['isDeadPoint'] && !$node['isStartPoint'] && !$node['isEndPoint']
                || empty($directions)) {
                return [];
            }

            $direction = $this->getBetterDirection($directions, $currentLocation, $nodes);
            $nextNodeLocation = $this->maze->getNodeLocationFromTo($currentLocation, $direction);

            if (empty($nextNodeLocation)
                || $nodes[$nextNodeLocation]['isDeadPoint']
                && !$nodes[$nextNodeLocation]['isEndPoint']) {
                return [];
            } else {
                $nodes = $this->getChangedStatesOnStepFor($currentLocation, $direction, $nodes);
                $currentLocation = $nextNodeLocation;
                $route[] = $currentLocation;
            }
            $stepsCounter++;
        }

        return $route;
    }


    /**
     * Create routes from starting point to end point testing different combinations of
     * available directions in crossPoints
     */
    private function setRoutes()
    {
        $routes = [];
        $states = $this->getStates();
        $initCrossPoints = $this->getCrossPoints();

        $this->maze->setNewStates($states);
        $newStates = $this->maze->getNewStates();
        $newCrossPoints = $this->maze->getCrossPointsFrom($newStates);

        if (empty($newCrossPoints)) {
            $routes[] = $this->getRouteFrom($newStates) ?? [];
        } else {
            $crossPointsOutOptions = $this->getRoutesOptionsFrom($initCrossPoints);
            foreach ($crossPointsOutOptions as $routeOption) {
                $this->maze->resetNewStatesExcept($initCrossPoints, $routeOption);
                $newStates = $this->maze->getNewStates();
                $routes[] = $this->getRouteFrom($newStates);
            }
        }

        $this->routes = $this->cleanRoutes($routes);
        $this->setOptimalRoutes();
    }

    /**
     * Generate all possible combinations of all crosspoints with forced direction outs
     * for testing all possible routes
     * @param  array  $crossPoints
     * @return array
     */
    public function getRoutesOptionsFrom(array $crossPoints): array
    {
        $startPoint = $this->maze->getStartPoint();
        $startNode = $this->maze->getNodeStateAt($startPoint);

        $crossPoints = $this->getCrossPoints();

        if (count($startNode['availableOuts']) > 1) {
            $crossPoints[$startPoint] = $startNode;
        }
        $crossPointOuts = [];
        foreach ($crossPoints as $location => $directions) {
            $crossPointOuts[$location] = $directions['availableOuts'];
        }

        $this->registerShutDownFunction();
        $this->permutationOfMultidimensionalArray(
            $crossPointOuts,
            function ($permutationIndex, $permutationArray) {
                $this->setCrossPointsOutOptions($permutationArray, $permutationIndex);
            }
        );


        return $this->getCrossPointsOutOptions();
    }


    /**
     * @source https://www.namasteui.com/permutation-of-multidimensional-array-in-php/
     * @param  array  $anArray
     * @param  false  $isValidCallback
     * @return array|int
     */
    private function permutationOfMultidimensionalArray(array $anArray, $isValidCallback = false)
    {
        if (empty($anArray)) {
            return 0;
        }
        $permutationCount = 1;
        $matrixInfo = array();
        $cumulativeCount = 1;
        foreach ($anArray as $aColumn) {
            $columnCount = count($aColumn);
            $permutationCount *= $columnCount;
            $matrixInfo[] = array(
                'count' => $columnCount,
                'cumulativeCount' => $cumulativeCount,
            );
            $cumulativeCount *= $columnCount;
        }
        $arrayKeys = array_keys($anArray);
        $matrix = array_values($anArray);
        $columnCount = count($matrix);
        $validPermutationCount = 0;
        $permutations = array();
        for ($currentPermutation = 0; $currentPermutation < $permutationCount; $currentPermutation++) {
            for ($currentColumnIndex = 0; $currentColumnIndex < $columnCount; $currentColumnIndex++) {
                $index = intval(
                        $currentPermutation / $matrixInfo[$currentColumnIndex]['cumulativeCount']
                    ) % $matrixInfo[$currentColumnIndex]['count'];
                $permutations[$currentPermutation][$currentColumnIndex] = $matrix[$currentColumnIndex][$index];
            }

            $permutations[$currentPermutation] = array_combine($arrayKeys, $permutations[$currentPermutation]);


            if ($isValidCallback !== false) {
                if ($isValidCallback($currentPermutation, $permutations[$currentPermutation])) {
                    $validPermutationCount++;
                }
            } else {
                $validPermutationCount++;
            }
            unset($permutations[$currentPermutation]);
        }
        if (!empty($permutations)) {
            return $permutations;
        } else {
            return $validPermutationCount;
        }
    }

    /**
     * @return array
     */
    public function getCrossPointsOutOptions(): array
    {
        return $this->crossPointsOutOptions;
    }

    /**
     * @param  array  $crossPointsOutOptions
     */
    public function setCrossPointsOutOptions(array $crossPointsOutOptions, $index): void
    {
        $this->crossPointsOutOptions[$index] = $crossPointsOutOptions;
    }

    /**
     * Get routes optimal routes with minimal steps amount
     * @return array
     */
    public function getOptimalRoutes(): array
    {
        return $this->optimalRoutes;
    }

    /**
     */
    public function setOptimalRoutes(): void
    {
        $optimalRoutes = [];
        $routes = $this->getRoutes();

        $minSteps = $this->getMaxStepsAmount();

        foreach ($routes as $route) {
            $routeSteps = count($route);
            if ($routeSteps === $minSteps) {
                $optimalRoutes[] = $route;
            }
            if (!empty($route) && $routeSteps < $minSteps) {
                $minSteps = $routeSteps;
                $optimalRoutes = [];//reset
                $optimalRoutes[] = $route;
            }
        }
        $this->optimalRoutes = array_unique($optimalRoutes, SORT_REGULAR);
    }


    /**
     * @param  array  $routes
     * @return array
     */
    private function cleanRoutes(array $routes)
    {
        return (array_filter(
            array_unique($routes, SORT_REGULAR),
            function ($route) {
                return !empty($route);
            }
        ));
    }

    /**
     * Filters states array and returns keys of the allowed directions
     *
     * @param  array  $states  node states, ['left'=>0, 'right'=>1, 'up'=>1,'down'=>0]
     * @return array available directions like ['left', 'down']
     */
    public function getAllowedDirectionsFrom(array $states): array
    {
        $directions = array_filter(
            $states,
            function ($state) {
                return 0 === $state;
            }
        );

        return array_keys($directions);
    }

    /**
     * @return array
     */
    public function getStates(): array
    {
        return $this->maze->getInitStatesMap();
    }

    /**
     * Change states of nodes on every move through maze
     * @param  string  $currentLocation
     * @param  string  $direction
     * @param  array  $nodes
     * @return array
     */
    public function getChangedStatesOnStepFor(string $currentLocation, string $direction, array $nodes): array
    {
        $nodes[$currentLocation]['states'][$direction] = 1;
        $nodes[$currentLocation]['isPassed'] = true;

        $nextNodeLocation = $this->maze->getNodeLocationFromTo($currentLocation, $direction);

        switch ($direction) {
            case 'up':

                $nodes[$nextNodeLocation]['states']['down'] = 1;
                break;
            case 'down':

                $nodes[$nextNodeLocation]['states']['up'] = 1;
                break;
            case 'left':

                $nodes[$nextNodeLocation]['states']['right'] = 1;
                break;
            case 'right':

                $nodes[$nextNodeLocation]['states']['left'] = 1;
                break;
        }


        return $nodes;
    }

    /**
     * @return array
     */
    public function getCrossPoints(): array
    {
        return $this->maze->getCrossPoints();
    }

    /**
     * @return array
     */
    public function getCrossPointsLocations(): array
    {
        return array_keys($this->maze->getCrossPoints());
    }


    /**
     * Selects better direction depending on the node state, available outs or forced outs
     *
     * @param  array  $directions
     * @param  string  $currentLocation
     * @param  array  $nodes
     * @return string
     */
    public function getBetterDirection(array $directions, string $currentLocation, array $nodes): string
    {
        //required
        if (!empty($nodes[$currentLocation]['forcedOut'])) {
            return $nodes[$currentLocation]['forcedOut'][0];
        }
        //if there is no forced out and there is a few Out options
        $directionRates = [
            'up' => 20,
            'down' => 50,
            'left' => 10,
            'right' => 40,
        ];
        $maxRatedDirection = 0;
        $selectedDirection = '';

        foreach ($directions as $direction) {
            if ($maxRatedDirection < $directionRates[$direction]) {
                $nextNodeLocation = $this->maze->getNodeLocationFromTo($currentLocation, $direction);

                if ($nodes[$nextNodeLocation]['isEndPoint']) {
                    return $direction;
                }

                if ($this->isDirectionSafe($direction, $currentLocation, $nodes)) {
                    $maxRatedDirection = $directionRates[$direction];
                    $selectedDirection = $direction;
                }
            }
        }

        return $selectedDirection;
    }

    /**
     * Test if the next node is not dead and can be passed
     * @param  string  $direction
     * @param  string  $location
     * @param  array  $states
     * @return bool
     */
    public function isDirectionSafe(string $direction, string $location, array $states): bool
    {
        $newLocation = $this->maze->getNodeLocationFromTo($location, $direction);

        return !$states[$newLocation]['isDeadPoint'] && !$states[$newLocation]['isPassed'];
    }

}