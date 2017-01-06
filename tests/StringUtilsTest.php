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

use PHPUnit_Framework_TestCase as TestCase;
use TTIC\Commons\StringUtils;

/**
 * StringUtils Tests
 *
 * @covers \TTIC\Commons\StringUtils
 */
class StringUtilsTest extends TestCase
{
    public function testStartsWith()
    {
        $data = [
            ['one string', 'one', true],
            ['another string', 'another', true],
            ['another string', 'one', false],
            ['another string', '', true],
        ];

        foreach ($data as $check) {
            $this->assertEquals($check[2], StringUtils::startsWith($check[0], $check[1]));
        }
    }

    public function testEndsWith()
    {
        $data = [
            ['one string', 'string', true],
            ['another strings', 'strings', true],
            ['another strinf', 'string', false],
            ['another strinf', '', true],
        ];

        foreach ($data as $check) {
            $this->assertEquals($check[2], StringUtils::endsWith($check[0], $check[1]));
        }
    }

    public function testCommonPrefix()
    {
        $data = [
            [['Uno', 'Undos', 'Untes'], 'Un'],
            [['Uno', 'Pndos', 'Untes'], ''],
            [['Ds_Uno', 'Ds_Pndos', 'Ds_Untes'], 'Ds_'],
        ];

        foreach ($data as $check) {
            $this->assertEquals($check[1], StringUtils::commonPrefix($check[0]));
        }
    }
}
