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

namespace TTIC\Commons;

/**
 * Common string utility class.
 * Some of them based on source code from:
 * http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php#answer-834355
 *
 * @package TTIC
 * @author Marcos Lois Bermúdez <marcos.lois@teratic.com>
 */
abstract class StringUtils
{
    /**
     * Check if a string has a prefix.
     *
     * @param $haystack string The input string.
     * @param $needle string The string to search.
     * @param bool $caseInsensitive If string comparision is made case insensitive.
     * @return bool true if the needle if found, false otherwise
     */
    public static function startsWith($haystack, $needle, $caseInsensitive = false)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return ($caseInsensitive ? (strcasecmp(substr($haystack, 0, $length), $needle) == 0)
            : (substr($haystack, 0, $length) === $needle));
    }

    /**
     * Check if a string has a suffix.
     *
     * @param $haystack string The input string.
     * @param $needle string The string to search.
     * @param bool $caseInsensitive If string comparision is made case insensitive.
     * @return bool true if the needle if found, false otherwise
     */
    public static function endsWith($haystack, $needle, $caseInsensitive = false)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return ($caseInsensitive ? (strcasecmp(substr($haystack, -$length), $needle) == 0)
            : (substr($haystack, -$length) === $needle));
    }

    /**
     * Get the common prefix of a list of strings.
     *
     * @param array $strings List of string to get prefix of.
     * @param bool $caseInsensitive If string comparision is made case insensitive.
     * @return string The common prefix to all strings or a empty string.
     */
    public static function commonPrefix(array $strings, $caseInsensitive = false)
    {
        sort($strings);

        $s1 = array_shift($strings);
        $s2 = array_pop($strings);
        $len = min(strlen($s1), strlen($s2));

        for ($i = 0; $i < $len && ($caseInsensitive ? strcasecmp($s1[$i], $s2[$i]) == 0 : $s1[$i] === $s2[$i]); $i++);

        return substr($s1, 0, $i);
    }
}
