<?php


namespace tests;


use MazeKiller\core\Maze;
use PHPUnit\Framework\TestCase;

/**
 * Class MazeTest
 *
 * @package tests
 */
class MazeTest extends TestCase
{

    /**
     * @param  array  $array
     * @param       $expCols
     *
     * @dataProvider providerColsData
     */
//    public function testGetCols(array $array, $expCols)
//    {
//        $maze = new Maze($array);
//
//        $cols = $maze->getCols();
//        $this->assertEquals($expCols, $cols);
//    }

    /**
     * @return array
     */
    public function providerColsData(): array
    {
        return [
            'test #1' => [
                [
                    ['.', '.'],
                    ['.', '.'],
                ],
                [
                    [0, 0],
                    [0, 0],
                ],
            ],

            'test #2' => [
                [
                    ['.', '#', '.'],
                    ['.', '.', '.'],
                    ['#', '.', '.'],
                ],
                [
                    [0, 0, 1],
                    [1, 0, 0],
                    [0, 0, 0],
                ],
            ],

        ];
    }

    /**
     * @param  array  $array
     * @param       $expected
     *
     * @dataProvider providerRowsData
     */
//    public function testGetRows(array $array, $expected)
//    {
//        $maze = new Maze($array);
//
//        $rows = $maze->getRows();
//        $this->assertEquals($expected, $rows);
//    }

    /**
     * @return array
     */
    public function providerRowsData(): array
    {
        return [
            'test rows - #1' => [
                [
                    ['.', '.'],
                    ['#', '.'],
                ],
                [
                    [0, 0],
                    [1, 0],
                ],
            ],

            'test rows - #2' => [
                [
                    ['.', '#', '.'],
                    ['.', '.', '.'],
                    ['#', '.', '.'],
                ],
                [
                    [0, 1, 0],
                    [0, 0, 0],
                    [1, 0, 0],
                ],
            ],

        ];
    }

    /**
     *
     */
    public function testGetColsNumber(): void
    {
        $array = [
            ['.', '.', '.'],
            ['.', '.', '.'],
            ['#', '.', '#'],
            ['.', '.', '.'],
        ];
        $maze = new Maze($array);

        $colsNum = $maze->getColsNumber();
        $this->assertEquals(3, $colsNum);

        $array = [
            ['.', '.',],
            ['.', '.',],
            ['#', '.',],
            ['.', '.',],
        ];
        //test setData
        $maze->setData($array);
        $colsNum = $maze->getColsNumber();
        $this->assertEquals(2, $colsNum);
        $this->assertNotEquals(3, $colsNum);
    }

    /**
     *
     */
    public function testGetEndPoint(): void
    {
        $array = [
            ['.', '.', '.'],
            ['.', '.', '.'],
            ['#', '.', '#'],
            ['.', '.', '.'],
        ];
        $maze = new Maze($array);

        $endPoint = $maze->getEndPoint();
        $this->assertEquals([3, 2], $endPoint);
    }

    /**
     *
     */
    public function testGetInitStatesMap()
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
                    'left' => 1,
                    'right' => 0,
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
            ],
            '01' => [
                'states' => [
                    'up' => 1,
                    'down' => 0,
                    'left' => 0,
                    'right' => 1,
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
            '10' => [
                'states' => [
                    'up' => 1,
                    'down' => 1,
                    'left' => 1,
                    'right' => 1,
                ],
                'availableOuts' => [],
                'forcedOut' => [],
                'isPassAble' => false,
                'isPassed' => false,
                'isCrossPoint' => false,
                'isStartPoint' => false,
                'isNearStartPoint' => false,
                'nearStartPointOn' => [],
                'isEndPoint' => false,
                'isNearEndPoint' => false,
                'nearEndPointOn' => [],
                'isDeadPoint' => true,
                'nearDeadPointsOn' => [],

            ],
            '11' => [
                'states' => [
                    'up' => 0,
                    'down' => 1,
                    'left' => 1,
                    'right' => 1,
                ],
                'availableOuts' => [],
                'forcedOut' => [],
                'isPassAble' => false,
                'isPassed' => false,
                'isCrossPoint' => false,
                'isStartPoint' => false,
                'isNearStartPoint' => false,
                'nearStartPointOn' => [],
                'isEndPoint' => true,
                'isNearEndPoint' => false,
                'nearEndPointOn' => [],
                'isDeadPoint' => true,
                'nearDeadPointsOn' => [],
            ],
        ];
        $maze = new Maze($array);

        $statesMap = $maze->getInitStatesMap();
        $this->assertEquals($expected, $statesMap);
    }

    public function testGetNodeLocationByFrom()
    {
        $array = [
            ['.', '.', '.'],
            ['#', '.', '#'],
            ['.', '.', '.'],
        ];
        $maze = new Maze($array);
        $this->assertEquals(
            '00',
            $maze->getNodeLocationFromTo('01', 'left')
        );
        $this->assertEquals(
            '02',
            $maze->getNodeLocationFromTo('01', 'right')
        );
        $this->assertEquals(
            '11',
            $maze->getNodeLocationFromTo('01', 'down')
        );
    }

    public function testIsSafeDirectionFromTo()
    {
        $array = [
            ['.', '.', '.'],
            ['.', '.', '#'],
            ['#', '.', '#'],
            ['.', '.', '.'],
        ];
        $maze = new Maze($array);
        $this->assertFalse(
            $maze->isSafeDirectionFromTo('01', 'right')
        );
        $this->assertTrue(
            $maze->isSafeDirectionFromTo('11', 'left')
        );
        $this->assertFalse(
            $maze->isSafeDirectionFromTo('11', 'right')
        );
        $this->assertFalse(
            $maze->isSafeDirectionFromTo('01', 'left')
        );
        $this->assertFalse(
            $maze->isSafeDirectionFromTo('21', 'left')
        );
        $this->assertFalse(
            $maze->isSafeDirectionFromTo('31', 'left')
        );
        $this->assertTrue(
            $maze->isSafeDirectionFromTo('31', 'right')
        );
    }

    public function testGetAvailableOutsFrom()
    {
        $array = [
            ['.', '.', '.', '.'],
            ['#', '.', '#', '.'],
            ['.', '.', '.', '.'],
            ['#', '.', '.', '.'],

        ];
        $maze = new Maze($array);

        $this->assertEquals(
            ['down', 'right'],
            $maze->getAvailableOutsFrom('01')
        );
        $this->assertEquals(
            ['up', 'down', 'right'],
            $maze->getAvailableOutsFrom('21')
        );
        $this->assertEquals(
            ['up', 'down', 'left'],
            $maze->getAvailableOutsFrom('23')
        );
    }

    public function testIsNearStartPointFrom()
    {
        $array = [
            ['.', '.', '.', '.'],
            ['#', '.', '#', '.'],
            ['.', '.', '.', '.'],
            ['#', '.', '.', '.'],

        ];
        $maze = new Maze($array);
        $this->assertTrue(
            $maze->isNearToNodeFrom('01', 'startPoint')
        );
        $this->assertFalse(
            $maze->isNearToNodeFrom('02', 'startPoint')
        );
    }

    public function testGetNodeStateAt()
    {
        $array = [
            ['.', '.', '.'],
            ['#', '.', '#'],
            ['.', '.', '.'],
        ];
        $maze = new Maze($array);

        $location = '11';
        $expected = [
            'states' => [
                'up' => 0,
                'down' => 0,
                'left' => 1,
                'right' => 1,
            ],
            'availableOuts' => ['up', 'down'],
            'forcedOut' => '',
            'isPassAble' => true,
            'isPassed' => false,
            'isCrossPoint' => false,
            'isStartPoint' => false,
            'isNearStartPoint' => false,
            'nearStartPointOn' => [],
            'isEndPoint' => false,
            'isNearEndPoint' => false,
            'nearEndPointOn' => [],
            'isDeadPoint' => false,
            'nearDeadPointsOn' => [],

        ];

        //self::assertEquals($expected, $maze->getNodeStateAt($location), 'at 11');
        $location = '01';
        $expected = [
            'states' => [
                'up' => 1,
                'down' => 0,
                'left' => 0,
                'right' => 0,
            ],
            'availableOuts' => ['down'],
            'forcedOut' => ['down'],
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
            'nearDeadPointsOn' => ['right'],

        ];
        self::assertEquals(
            $expected,
            $maze->getNodeStateAt($location),
            'at 01'
        );

        $location = '02';
        $expected = [
            'states' => [
                'up' => 1,
                'down' => 1,
                'left' => 0,
                'right' => 1,
            ],
            'availableOuts' => [],
            'forcedOut' => [],
            'isPassAble' => false,
            'isPassed' => false,
            'isCrossPoint' => false,
            'isStartPoint' => false,
            'isNearStartPoint' => false,
            'nearStartPointOn' => [],
            'isEndPoint' => false,
            'isNearEndPoint' => false,
            'nearEndPointOn' => [],
            'isDeadPoint' => true,
            'nearDeadPointsOn' => [],

        ];
        self::assertEquals($expected, $maze->getNodeStateAt($location), 'at 02');
    }

    public function testIsAvailableNodeAt()
    {
        $array = [
            ['.', '.', '.'],
            ['#', '.', '#'],
            ['.', '.', '.'],
        ];
        $location = '11';
        $maze = new Maze($array);

        $this->assertTrue($maze->isPassAbleNodeAt($location));

        $location = '10';
        $this->assertFalse($maze->isPassAbleNodeAt($location));

        $location = '00';
        $this->assertFalse($maze->isPassAbleNodeAt($location));
        $location = '21';
        $this->assertTrue($maze->isPassAbleNodeAt($location));

        $location = '22';
        $this->assertFalse($maze->isPassAbleNodeAt($location));
    }

    public function testIsCrossPoint()
    {
        $array = [
            ['.', '.', '.'],
            ['#', '.', '#'],
            ['.', '.', '.'],
        ];
        $maze = new Maze($array);

        $this->assertTrue($maze->isCrossPoint('01'));
        $this->assertFalse($maze->isCrossPoint('00'));
        $this->assertFalse($maze->isCrossPoint('11'));
        $this->assertFalse($maze->isCrossPoint('10'));
        $this->assertTrue($maze->isCrossPoint('21'));
    }

    public function testGetCrossPoints()
    {
        $array = [
            ['.', '.', '.', '.'],
            ['#', '.', '#', '#'],
            ['.', '.', '.', '.'],
        ];
        $expected = [
            '01' => [
                'states' => [
                    'up' => 1,
                    'down' => 0,
                    'left' => 0,
                    'right' => 0,
                ],
                'availableOuts' => ['down', 'right'],
                'forcedOut' => [],
                'isPassed' => false,
                'isStartPoint' => false,
                'isEndPoint' => false,
                'isDeadPoint' => false,
                'isNearEndPoint' => false,
                'isNearStartPoint' => true,
                'nearDeadPointsOn' => [],

                'isPassAble' => true,
                'isCrossPoint' => true,
                'nearStartPointOn' => ['left'],
                'nearEndPointOn' => [],

            ],
            '21' => [
                'states' => [
                    'up' => 0,
                    'down' => 1,
                    'left' => 0,
                    'right' => 0,
                ],
                'availableOuts' => ['up', 'right'],
                'isPassed' => false,
                'isStartPoint' => false,
                'isEndPoint' => false,
                'isDeadPoint' => false,
                'isNearEndPoint' => false,
                'isNearStartPoint' => false,
                'nearDeadPointsOn' => ['left'],
                'forcedOut' => [],
                'isPassAble' => true,
                'isCrossPoint' => true,
                'nearStartPointOn' => [],
                'nearEndPointOn' => [],
            ],
        ];
        $maze = new Maze($array);

        $this->assertEquals($expected, $maze->getCrossPoints());
    }

    public function testIsPassPoint()
    {
        $array = [
            ['.', '.', '.'],
            ['#', '.', '#'],
            ['.', '.', '.'],
        ];
        $maze = new Maze($array);

        $this->assertFalse($maze->isPassPoint('01'));
        $this->assertFalse($maze->isPassPoint('00'));
        $this->assertTrue($maze->isPassPoint('11'));
        $this->assertFalse($maze->isPassPoint('10'));
        $this->assertFalse($maze->isPassPoint('21'));
    }

    public function testIsDeadPoint()
    {
        $array = [
            ['.', '.', '.'],
            ['#', '.', '#'],
            ['.', '.', '.'],
        ];
        $maze = new Maze($array);

        $this->assertFalse($maze->isDeadPoint('01'), 'DeadPoint 01?');
        $this->assertTrue($maze->isDeadPoint('02'), 'DeadPoint 02?');
        $this->assertFalse($maze->isDeadPoint('11'), 'DeadPoint 11?');
        $this->assertTrue($maze->isDeadPoint('10'), 'DeadPoint 10?');
        $this->assertTrue($maze->isDeadPoint('20'), 'DeadPoint 21?');
        $this->assertFalse($maze->isDeadPoint('22'), 'DeadPoint 22?');
    }

    public function testGetNewStates()
    {
        $array = [
            ['.', '.', '.'],
            ['#', '.', '#'],
            ['.', '.', '.'],
        ];
        $maze = new Maze($array);

        $newStates = $maze->getInitStatesMap();
        $newStates['00']['states']['right'] = 1;
        $maze->setNewStates($newStates);

        $newReceivedStates = $maze->getNewStates();
        $initStates = $maze->getInitStatesMap();
        $this->assertNotEquals($newReceivedStates, $initStates);

        $this->assertEquals(1, $newReceivedStates['00']['states']['right']);
    }

    public function testGetCrossPointsFrom()
    {
        $array = [
            ['.', '.', '.'],
            ['#', '.', '#'],
            ['.', '.', '.'],
        ];
        $expected = ['21'];
        $maze = new Maze($array);

        $newStates = $maze->getInitStatesMap();
        $newStates['01']['states']['right'] = 1;

        $crossPoints = $maze->getCrossPointsFrom($newStates);

        $this->assertEquals($expected, $crossPoints);

        $newStates['21']['states']['right'] = 1;
        $crossPoints = $maze->getCrossPointsFrom($newStates);
        $expected = [];

        $this->assertEquals($expected, $crossPoints);
    }

    public function testResetNewStatesExcept()
    {
        $array = [
            ['.', '.', '.'],
            ['#', '.', '#'],
            ['.', '.', '.'],
        ];

        $maze = new Maze($array);
        $newStates = $maze->getInitStatesMap();
        $initCrossPoints = $maze->getCrossPointsLocations();


        $newStates['01']['states']['right'] = 1;//crossPoint
        $forcedDirection = ['down'];
        $newStates['01']['forcedOut'] = $forcedDirection;
        $expected = $newStates;

        $newStates['00']['states']['right'] = 1;

        $maze->setNewStates($newStates);

        $maze->resetNewStatesExcept($initCrossPoints,$forcedDirection);
        $newStates = $maze->getNewStates();
        $this->assertEquals($expected['01']['forcedOut'], $newStates['01']['forcedOut']);
    }
}