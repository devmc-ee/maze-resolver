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
    public function testGetOptimalRoutes(array $array, array $expected)
    {
        $mazeSolver = new MazeSolver(new Maze($array));

        $this->assertEquals(
            $expected,
            $mazeSolver->getOptimalRoutes()
        );
    }

    /**
     * @see testGetRoutes()
     */
    public function provideTestData()
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
                    [['01', '11', '21', '20', '30', '40', '41']],
                ],
            'test2: routes with CrossPoints:' =>
                [
                    [
                        ['.', '.'],
                        ['.', '#'],
                        ['.', '.'],
                    ],
                    [
                        ['10', '20', '21'],


                    ],
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
                        0 => ['10', '11', '21', '31', '32'],
                        1 => ['01', '11', '21', '31', '32'],
                    ],
                ],
            'test4: routes with CrossPoints:' =>
                [
                    [
                        ['.', '.', '.', '.'],
                        ['.', '.', '#', '.'],
                        ['#', '.', '#', '.'],
                        ['.', '#', '.', '.'],
                    ],

                    [['01', '02', '03', '13', '23', '33']],
                ],

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
                'down' => 1,
                'right' => 0,
                'left' => 1,
            ],
            'availableOuts' => ['right'],
            'forcedOut' => ['right'],
            'isPassAble' => false,
            'isPassed' => false,
            'isCrossPoint' => false,
            'isStartPoint' => true,
            'isNearStartPoint' => false,
            'nearStartPointOn' => [],
            'isEndPoint' => false,
            'isNearEndPoint' => false,
            'nearEndPointOn' => [],
            'isDeadPoint' => true,
            'nearDeadPointsOn' => [],
        ];

        $mazeSolver = new MazeSolver(new Maze($array));
        $this->assertEquals($expected, $mazeSolver->getStates()['00']);
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
                '00' => 'down',
                '11' => 'up',
                '01' => 'down',
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
            ],
        ];
        $maze = new Maze($array);
        $node = $maze->getNodeStateAt('00');
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
            $mazeSolver->getCrossPoints()
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
                        '01' => [
                            'states' => [
                                'up' => 1,
                                'down' => 0,
                                'left' => 0,
                                'right' => 0,
                            ],

                            'availableOuts' => [ 'down', 'right'],
                            'forcedOut' => [],
                            'isPassAble' => true,
                            'isPassed' => false,
                            'isCrossPoint' => true,
                            'isStartPoint' => false,
                            'isNearStartPoint' => true,
                            'nearStartPointOn' => ['left'],
                            'isEndPoint' => false,
                            'isNearEndPoint' => false,
                            'nearEndPointOn' => [],
                            'isDeadPoint' => false,
                            'nearDeadPointsOn' => [],
                        ],
                        '11'=> [
                            'states' => [
                                'up' => 0,
                                'down' => 0,
                                'left' => 0,
                                'right' => 0,
                            ],

                            'availableOuts' => ['up', 'down','left', 'right'],
                            'forcedOut' => [],
                            'isPassAble' => true,
                            'isPassed' => false,
                            'isCrossPoint' => true,
                            'isStartPoint' => false,
                            'isNearStartPoint' => false,
                            'nearStartPointOn' => [],
                            'isEndPoint' => false,
                            'isNearEndPoint' => false,
                            'nearEndPointOn' => [],
                            'isDeadPoint' => false,
                            'nearDeadPointsOn' => [],
                        ],
                        '31'=> [
                            'states' => [
                                'up' => 0,
                                'down' => 1,
                                'left' => 0,
                                'right' => 0,
                            ],

                            'availableOuts' => [ 'up', 'right'],
                            'forcedOut' => [],
                            'isPassAble' => true,
                            'isPassed' => false,
                            'isCrossPoint' => true,
                            'isStartPoint' => false,
                            'isNearStartPoint' => false,
                            'nearStartPointOn' => [],
                            'isEndPoint' => false,
                            'isNearEndPoint' => true,
                            'nearEndPointOn' => ['right'],
                            'isDeadPoint' => false,
                            'nearDeadPointsOn' => ['left'],
                        ],
                    ],
                ],

        ];
    }
}