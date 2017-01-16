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

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;
use TTIC\Commons\Iterator\CallbackMapIterator;

/**
 * CallbackMapIterator Tests
 *
 * @covers \TTIC\Commons\Iterator\CallbackMapIterator
 */
class CallbackMapIteratorTest extends TestCase
{
    public function testThatCallbackIsCalledAndApplied()
    {
        // Given: A Iterator of values
        $values = new ArrayIterator([1, 4, 6, 7, 8]);
        $called = 0;

        // When: A CallbackMapIterator is create over the iterator
        $iterator = new CallbackMapIterator($values, function($current) use (&$called) {
            $called++;
            return $current * 2;
        });

        // Then: All the values are filtered
        $this->assertEquals([2, 8, 12, 14, 16], iterator_to_array($iterator));
        $this->assertEquals(5, $called);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThatInvalidCallbackIsDetected()
    {
        // Given: A Iterator of values
        $values = new ArrayIterator([1, 4, 6, 7, 8]);

        // When: A CallbackMapIterator is created with a invalid callback
        new CallbackMapIterator($values, [$this, 'no-defined']);

        // Then: A excepotion is thrown
    }
}
