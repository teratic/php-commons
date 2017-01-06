<?php
/*
* The MIT License (MIT)
* Copyright © 2017 Marcos Lois Bermudez <marcos.lois@teratic.com>
* *
* Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
* documentation files (the “Software”), to deal in the Software without restriction, including without limitation
* the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
* and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
* *
* The above copyright notice and this permission notice shall be included in all copies or substantial portions
* of the Software.
* *
* THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
* TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
* THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
* CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
* DEALINGS IN THE SOFTWARE.
*/

namespace TTIC\Commons\Iterator\Test;

use Iterator;
use PHPUnit_Framework_TestCase as TestCase;
use TTIC\Commons\Iterator\SyncOrderedIterator;

/**
 * SyncOrderedIterator Tests
 *
 * @covers \TTIC\Commons\Iterator\SyncOrderedIterator
 */
class SyncOrderedIteratorTest extends TestCase
{
    public function testSyncOrderedIterator()
    {
        $left = new \ArrayIterator([1, 4, 6, 8]);
        $right = new \ArrayIterator([1, 2, 4, 5, 6, 7, 9]);

        $sync = new SyncOrderedIterator($left, $right);
        $result = iterator_to_array($sync);

        $this->assertNotEmpty($result);
        $this->assertCount(8, $result);
        $this->assertEquals([
            [
                'type' => SyncOrderedIterator::EQUAL,
                'left' => ['key' => 0, 'value' => 1],
                'right' => ['key' => 0, 'value' => 1]
            ],
            [
                'type' => SyncOrderedIterator::LEFT_MISSING,
                'item' => ['key' => 1, 'value' => 2]
            ],
            [
                'type' => SyncOrderedIterator::EQUAL,
                'left' => ['key' => 1, 'value' => 4],
                'right' => ['key' => 2, 'value' => 4]
            ],
            [
                'type' => SyncOrderedIterator::LEFT_MISSING,
                'item' => ['key' => 3, 'value' => 5]
            ],
            ['type' => SyncOrderedIterator::EQUAL,
                'left' => ['key' => 2, 'value' => 6],
                'right' => ['key' => 4, 'value' => 6]
            ],
            [
                'type' => SyncOrderedIterator::LEFT_MISSING,
                'item' => ['key' => 5, 'value' => 7]
            ],
            [
                'type' => SyncOrderedIterator::RIGHT_MISSING,
                'item' => ['key' => 3, 'value' => 8]
            ],
            [
                'type' => SyncOrderedIterator::LEFT_MISSING,
                'item' => ['key' => 6, 'value' => 9]
            ],
        ], $result);

    }

    public function testSyncOrderedIteratorWithPkComparator()
    {

        $left = new \ArrayIterator([1 => 'a', 2 => 'B', 4 => 'd', 5 => 'E', 6 => 'f', 7 => 'G', 9 => 'h']);
        $right = new \ArrayIterator([1 => 'a', 4 => 'D', 6 => 'F', 8 => 'h']);

        $sync = new SyncOrderedIterator($left, $right, function (Iterator $left, Iterator $right) {
            $lItem = $left->key();
            $rItem = $right->key();

            return ($lItem == $rItem ? 0 : (($lItem < $rItem) ? -1 : 1));
        });
        $result = iterator_to_array($sync);

        $this->assertNotEmpty($result);
        $this->assertCount(8, $result);
        $this->assertEquals([
            [
                'type' => SyncOrderedIterator::EQUAL,
                'left' => ['key' => 1, 'value' => 'a'],
                'right' => ['key' => 1, 'value' => 'a']
            ],
            [
                'type' => SyncOrderedIterator::RIGHT_MISSING,
                'item' => ['key' => 2, 'value' => 'B']
            ],
            [
                'type' => SyncOrderedIterator::EQUAL,
                'left' => ['key' => 4, 'value' => 'd'],
                'right' => ['key' => 4, 'value' => 'D']
            ],
            [
                'type' => SyncOrderedIterator::RIGHT_MISSING,
                'item' => ['key' => 5, 'value' => 'E']
            ],
            [
                'type' => SyncOrderedIterator::EQUAL,
                'left' => ['key' => 6, 'value' => 'f'],
                'right' => ['key' => 6, 'value' => 'F']
            ],
            [
                'type' => SyncOrderedIterator::RIGHT_MISSING,
                'item' => ['key' => 7, 'value' => 'G']
            ],
            [
                'type' => SyncOrderedIterator::LEFT_MISSING,
                'item' => ['key' => 8, 'value' => 'h']
            ],
            [
                'type' => SyncOrderedIterator::RIGHT_MISSING,
                'item' => ['key' => 9, 'value' => 'h']
            ],
        ], $result);

    }

    public function testSyncOrderedIteratorWithItemEquals()
    {
        $left = new \ArrayIterator([1 => 'a', 4 => 'D', 6 => 'F', 8 => 'h']);
        $right = new \ArrayIterator([1 => 'a', 2 => 'B', 4 => 'd', 5 => 'E', 6 => 'f', 7 => 'G', 9 => 'h']);

        $sync = new SyncOrderedIterator($left, $right, function (\Iterator $left, \Iterator $right) {
            $lItem = $left->key();
            $rItem = $right->key();

            return ($lItem == $rItem ? 0 : (($lItem < $rItem) ? -1 : 1));
        }, function (Iterator $left, Iterator $right) {
            return strcmp($left->current(), $right->current()) == 0;
        });
        $result = iterator_to_array($sync);

        $this->assertNotEmpty($result);
        $this->assertCount(8, $result);
        $this->assertEquals([
            [
                'type' => SyncOrderedIterator::EQUAL,
                'left' => ['key' => 1, 'value' => 'a'],
                'right' => ['key' => 1, 'value' => 'a']
            ],
            [
                'type' => SyncOrderedIterator::LEFT_MISSING,
                'item' => ['key' => 2, 'value' => 'B']
            ],
            [
                'type' => SyncOrderedIterator::NOT_EQUAL,
                'left' => ['key' => 4, 'value' => 'D'],
                'right' => ['key' => 4, 'value' => 'd']
            ],
            [
                'type' => SyncOrderedIterator::LEFT_MISSING,
                'item' => ['key' => 5, 'value' => 'E']
            ],
            [
                'type' => SyncOrderedIterator::NOT_EQUAL,
                'left' => ['key' => 6, 'value' => 'F'],
                'right' => ['key' => 6, 'value' => 'f']
            ],
            [
                'type' => SyncOrderedIterator::LEFT_MISSING,
                'item' => ['key' => 7, 'value' => 'G']
            ],
            [
                'type' => SyncOrderedIterator::RIGHT_MISSING,
                'item' => ['key' => 8, 'value' => 'h']
            ],
            [
                'type' => SyncOrderedIterator::LEFT_MISSING,
                'item' => ['key' => 9, 'value' => 'h']
            ],
        ], $result);
    }
}
