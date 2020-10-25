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

    public function testOptimizeMazeValues()
    {
        $array = [
            ['.', '.', '#', '.'],
            ['.', '.', '.', '.'],
            ['#', '.', '#', '.'],
            ['.', '.', '.', '.'],
            ['.', '.', '.', '.'],
            ['.', '.', '.', '.'],
            ['.', '.', '.', '.'],
        ];

        $expected = [
            [0, 0, 1, 0],
            [0, 0, 0, 0],
            [1, 0, 1, 0],
            [0, 0, 0, 0],
            [0, 1, 0, 0],
            [0, 0, 0, 0],
            [0, 0, 0, 0],
        ];
        $maze = new Maze($array);
        self::assertEquals(
            $expected,
           $maze->getMaze(),
            'optimize  1.1'
        );

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
                'isOnEmptyPathToStartPoint' => true,
                'isOnEmptyPathToEndPoint' => false,
                'isOnEmptyPathNearEndPoint' => true,
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
                'isOnEmptyPathToStartPoint' => true,
                'isOnEmptyPathToEndPoint' => true,
                'isOnEmptyPathNearEndPoint' => true,
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
                'isOnEmptyPathToStartPoint' => false,
                'isOnEmptyPathToEndPoint' => false,
                'isOnEmptyPathNearEndPoint' => true,

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
                'isOnEmptyPathToStartPoint' => false,
                'isOnEmptyPathToEndPoint' => true,
                'isOnEmptyPathNearEndPoint' => false,
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

    public function testIsOnEmptyPathTo()
    {
        $array = [
            ['.', '.', '.', '.'],
            ['.', '.', '#', '.'],
            ['.', '.', '.', '.'],
            ['#', '.', '.', '.'],

        ];
        $maze = new Maze($array);

        self::assertTrue(
            $maze->isOnEmptyPathToFrom(
                'nearEndPoint',
                '22'
            ),
            'is on path column near to endPoint from 22'
        );
        self::assertTrue(
            $maze->isOnEmptyPathToFrom(
                'nearEndPoint',
                '21'
            ),
            'is on empty path near to endPoint from 21'
        );
        self::assertFalse(
            $maze->isOnEmptyPathToFrom(
                'nearEndPoint',
                '11'
            ),
            'is on empty path near to endPoint from 11'
        );
        self::assertFalse(
            $maze->isOnEmptyPathToFrom(
                'nearEndPoint',
                '02'
            ),
            'is on empty column near to endPoint from 02'
        );

        self::assertTrue(
            $maze->isOnEmptyPathToFrom(
                'endPoint',
                '03'
            ),
            'is on empty path to endPoint from 03'
        );
        self::assertFalse(
            $maze->isOnEmptyPathToFrom(
                'endPoint',
                '02'
            ),
            'is  on empty path to endPoint from 02'
        );
        self::assertTrue(
            $maze->isOnEmptyPathToFrom(
                'endPoint',
                '31'
            ),
            'is on empty path to endPoint from 31'
        );
        self::assertTrue(
            $maze->isOnEmptyPathToFrom(
                'startPoint',
                '20'
            ),
            'is on empty path to startPoint from 20'
        );
        self::assertTrue(
            $maze->isOnEmptyPathToFrom(
                'startPoint',
                '02'
            ),
            'is on empty path to startPoint from 02'
        );
    }

    public function testGetAvailableOutsFrom()
    {
        $array = [
            ['.', '.', '.', '.'],
            ['.', '.', '#', '.'],
            ['.', '.', '.', '.'],
            ['#', '.', '.', '.'],

        ];
        $maze = new Maze($array);

        $this->assertEquals(
            ['down', 'right'],
            $maze->getAvailableOutsFrom('01'),
            'getAvailableOutsFrom 01'
        );
        $this->assertEquals(
            ['right'],
            $maze->getAvailableOutsFrom('20'),
            'getAvailableOutsFrom 20'
        );


        $this->assertEquals(
            ['down', 'right'],
            $maze->getAvailableOutsFrom('21'),
            'getAvailableOutsFrom 21'
        );
        $this->assertEquals(
            ['down'],
            $maze->getAvailableOutsFrom('23'),
            'getAvailableOutsFrom 23'
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
            'isOnEmptyPathToStartPoint' => true,
    'isOnEmptyPathToEndPoint' => false,
    'isOnEmptyPathNearEndPoint' => true,

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
            'isOnEmptyPathToStartPoint' => true,
    'isOnEmptyPathToEndPoint' => false,
    'isOnEmptyPathNearEndPoint' => false,

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
            ['.', '.', '.'],
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
            ['.', '.', '.', '.'],
        ];
        $expected = [];
        $maze = new Maze($array);
        $crossPoints = $maze->getCrossPoints();
        $this->assertEquals(
            ['01', '21', '22'],
            array_keys($crossPoints));
        $this->assertEquals(['down','right'], $crossPoints['21']['availableOuts'],
        'test cross point at 21');
        $this->assertEquals(['down'], $crossPoints['01']['availableOuts'],
            'test cross point at 01');


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
        $crossPointsOld = $maze->getCrossPoints();
        $crossPoints = $maze->getCrossPointsFrom($newStates);

        $this->assertEquals([], $crossPoints,
            'GetCrossPointsFrom new states 1');

        $newStates['21']['states']['left'] = 1;
        $crossPoints = $maze->getCrossPointsFrom($newStates);
        $expected = [];

        $this->assertEquals($expected, $crossPoints,
            'GetCrossPointsFrom new states 2');
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

        $maze->resetNewStatesExcept($initCrossPoints, $forcedDirection);
        $newStates = $maze->getNewStates();
        $this->assertEquals($expected['01']['forcedOut'], $newStates['01']['forcedOut']);
    }


    public function testOptimizeNodes()
    {
        $array = [
            ['.', '.', '.'],
            ['.', '.', '.'],
            ['.', '.', '.'],
            ['.', '.', '.'],
            ['.', '.', '.'],
            ['.', '.', '.'],
        ];
        $maze = new Maze($array);

        $optimizedNodes = $maze->getInitStatesMap();

        self::assertEquals(
            ['down', 'right'],
            $optimizedNodes['01']['availableOuts'],
            'On empty row, 01:'
        );
        self::assertEquals(
            ['down'],
            $optimizedNodes['02']['availableOuts'],
            'On empty row, 02:'
        );
        self::assertEquals(
            ['down'],
            $optimizedNodes['12']['availableOuts'],
            'On empty col, 12:'
        );
        self::assertEquals(
            ['down', 'right'],
            $optimizedNodes['21']['availableOuts'],
            'On empty col, 21:'
        );
        self::assertEquals(
            ['right'],
            $optimizedNodes['51']['availableOuts'],
            'On empty col, 51:'
        );
    }

    public function testGelColumnsAndRows()

    {
        $array = [
            ['.', '.', '.'],
            ['.', '.', '.'],
            ['#', '.', '.'],
            ['.', '.', '.'],
        ];
        $maze = new Maze($array);
        $columns = $maze->getColumns();
        $rows = $maze->getRows();
        self::assertEquals(1, array_sum($columns[0]), 'get cols #1');
        self::assertEquals(0, array_sum($columns[1]), 'get cols #2');
        self::assertCount(4, $columns[0], 'get cols #3');
        self::assertEquals(0, array_sum($rows[0]), 'get rows #1');
        self::assertEquals(1, array_sum($rows[2]), 'get rows #2');
        self::assertCount(3, $rows[0], 'get rows #3');
    }


}