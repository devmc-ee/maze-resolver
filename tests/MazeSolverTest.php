<?php


namespace tests;

use Exception;
use MazeKiller\core\Maze;
use MazeKiller\core\MazeSolver;
use PHPUnit\Framework\TestCase;

class MazeSolverTest extends TestCase
{
    /**
     * @param  array  $array
     * @param  array  $expected
     *
     * @dataProvider provideTestData
     * @throws Exception
     */
    public function testGetOptimalRoutes(array $array, int $expected)
    {
        $mazeSolver = new MazeSolver(new Maze($array));

        $this->assertCount(
            $expected,
            $mazeSolver->getOptimalRoutes()[0]
        );
    }


    /**
     * @see testGetRoutes()
     */
    public function provideTestData()
    {
        return [
            'test #0' => [
                [
                    ['.', '.', '.', '.'],
                    ['.', '.', '.', '.'],
                    ['.', '.', '.', '.'],
                    ['.', '.', '.', '.'],
                    ['.', '.', '.', '.'],
                    ['.', '.', '.', '.'],
                    ['.', '.', '.', '.'],

                ],
                9,
            ],
            'test #6' => [
                [
                    ['.', '.', '#', '.'],
                    ['#', '.', '#', '.'],
                    ['.', '.', '.', '.'],
                    ['.', '#', '.', '.'],
                ],
                6,
            ],
            'test1: routes without CrossPoints:' =>
                [
                    [
                        ['.', '.'],
                        ['#', '.'],
                        ['.', '.'],
                        ['.', '#'],
                        ['.', '.'],
                    ],
                    7,
                ],
            'test2: routes with CrossPoints:' =>
                [
                    [
                        ['.', '.'],
                        ['.', '#'],
                        ['.', '.'],
                    ],
                    3,
                ],
            'test3: routes with CrossPoints:' =>
                [
                    [
                        ['.', '.', '.'],
                        ['.', '.', '.'],
                        ['#', '.', '#'],
                        ['.', '.', '.'],
                    ],
                    5,
                ],
            'test4: routes with CrossPoints:' =>
                [
                    [
                        ['.', '.', '.', '.'],
                        ['.', '.', '#', '.'],
                        ['#', '.', '#', '.'],
                        ['.', '#', '.', '.'],
                    ],

                    6,
                ],
//            'test5: routes with only empty paths 4 x4 :' =>
//                [
//                    [
//                        ['.', '.', '.', '.'],
//                        ['.', '.', '.', '.'],
//                        ['.', '.', '.', '.'],
//                        ['.', '.', '.', '.'],
//                    ],
//
//                    [['01', '02', '03', '13', '23', '33']],
//                ],


        ];
    }

    public function testGetStates()
    {
        $array = [
            ['.', '.'],
            ['#', '.'],

        ];

        $expected = [
            'states' => [
                'up' => 1,
                'down' => 0,
                'right' => 1,
                'left' => 0,
            ],
            'availableOuts' => ['down'],
            'forcedOut' => ['down'],
            'isPassAble' => true,
            'isPassed' => false,
            'isCrossPoint' => false,
            'isStartPoint' => false,
            'isNearStartPoint' => true,
            'nearStartPointOn' => ['left'],
            'isEndPoint' => false,
            'isNearEndPoint' => true,
            'nearEndPointOn' => ['down'],
            'isDeadPoint' => false,
            'nearDeadPointsOn' => [],
            'isOnEmptyPathToStartPoint' => true,
            'isOnEmptyPathToEndPoint' => true,
            'isOnEmptyPathNearEndPoint' => true,
        ];

        $mazeSolver = new MazeSolver(new Maze($array));
        $this->assertEquals($expected, $mazeSolver->getStates()['01']);
    }

    public function testGetRoutesOptionsFrom()
    {
        $array = [
            ['.', '.', '.', '.'],
            ['.', '.', '#', '.'],
            ['#', '.', '#', '.'],
            ['.', '#', '.', '.'],
        ];
        $expected = [];
        $maze = new Maze($array);
        $mazeSolver = new MazeSolver($maze);
        $crossPoints = $mazeSolver->getCrossPoints();
        $this->assertEquals(

            [
                '01' => 'down',
                '11' => 'up',
                '00' => 'down',

            ]
            ,
            $mazeSolver->getRoutesOptionsFrom($crossPoints)[0]
        );
    }


    public function testGetDirection()
    {
        $array = [
            ['.', '.'],
            ['#', '.'],

        ];

        $expected = ['right'];
        $maze = new Maze($array);
        $node = $maze->getNodeStateAt('00');

        $mazeSolver = new MazeSolver($maze);

        $this->assertEquals($expected, $mazeSolver->getAllowedDirectionsFrom($node['states']));
    }

    public function testGetChangedStatesOnStepFor()
    {
        $array = [
            ['.', '.'],
            ['#', '.'],
        ];
        $expected = [
            '00' => [
                'states' => [
                    'up' => 1,
                    'down' => 1,
                    'right' => 1,
                    'left' => 1,
                ],
                'availableOuts' => ['right'],
                'forcedOut' => ['right'],
                'isPassAble' => false,
                'isPassed' => true,
                'isCrossPoint' => false,
                'isStartPoint' => true,
                'isNearStartPoint' => false,
                'nearStartPointOn' => [],
                'isEndPoint' => false,
                'isNearEndPoint' => false,
                'nearEndPointOn' => [],
                'isDeadPoint' => true,
                'nearDeadPointsOn' => [],
                'isOnEmptyPathToStartPoint' => true,
                'isOnEmptyPathToEndPoint' => false,
                'isOnEmptyPathNearEndPoint' => true,
            ],
            '01' => [
                'states' => [
                    'up' => 1,
                    'down' => 0,
                    'right' => 1,
                    'left' => 1,
                ],
                'availableOuts' => ['down'],
                'forcedOut' => ['down'],
                'isPassAble' => true,
                'isPassed' => false,
                'isCrossPoint' => false,
                'isStartPoint' => false,
                'isNearStartPoint' => true,
                'nearStartPointOn' => ['left'],
                'isEndPoint' => false,
                'isNearEndPoint' => true,
                'nearEndPointOn' => ['down'],
                'isDeadPoint' => false,
                'nearDeadPointsOn' => [],
                'isOnEmptyPathToStartPoint' => true,
                'isOnEmptyPathToEndPoint' => true,
                'isOnEmptyPathNearEndPoint' => true,
            ],
        ];
        $maze = new Maze($array);

        $states = $maze->getInitStatesMap();

        $mazeSolver = new MazeSolver($maze);
        $newStates = $mazeSolver->getChangedStatesOnStepFor('00', 'right', $states);
        $this->assertEquals($expected['00'], $newStates['00'], 'new state in 00 after step');
        $this->assertEquals($expected['01'], $newStates['01'], 'new state in 01 after step');
    }

    /**
     *
     * @dataProvider provideTestDataForGetCrossPoints
     *
     * @param  array  $array
     * @param  array  $expected
     */
    public function testGetCrossPoints(array $array, array $expected)
    {
        $mazeSolver = new MazeSolver(new Maze($array));

        $this->assertEquals(
            $expected,
            array_keys($mazeSolver->getCrossPoints())
        );
    }

    /**
     * @see testGetCrossPoints()
     */
    public function provideTestDataForGetCrossPoints()
    {
        return [
            'test1: routes without CrossPoints:' =>
                [
                    [
                        ['.', '.'],
                        ['#', '.'],
                        ['.', '.'],
                        ['.', '#'],
                        ['.', '.'],
                    ],
                    [],
                ],
            'test2: routes with no CrossPoints:' =>
                [
                    [
                        ['.', '.'],
                        ['.', '#'],
                        ['.', '.'],
                    ],
                    [],
                ],
            'test3: routes with CrossPoints:' =>
                [
                    [
                        ['.', '.', '.'],
                        ['.', '.', '.'],
                        ['#', '.', '#'],
                        ['.', '.', '.'],
                    ],
                    [
                        '01',
                        '11',

                    ],
                ],

        ];
    }
}