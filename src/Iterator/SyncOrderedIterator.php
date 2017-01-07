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

namespace TTIC\Commons\Iterator;

use Iterator;

class SyncOrderedIterator implements Iterator
{
    const LEFT_MISSING = 'left-missing';
    const RIGHT_MISSING = 'right-missing';
    const EQUAL = 'equal';
    const NOT_EQUAL = 'not-equal';

    /**
     * Left side iterator
     * @var Iterator
     */
    protected $left;

    /**
     * Right side iterator
     * @var Iterator
     */
    protected $right;

    /**
     * Current operation
     * @var mixed
     */
    protected $operation;

    /**
     * @var int
     */
    protected $key = 0;

    /**
     * @var callable
     */
    protected $keyCmpCallback;
    /**
     * @var callable
     */
    protected $valueEqualsCallback;

    /**
     * SyncOrderedIterator constructor.
     *
     * @param Iterator $left
     * @param Iterator $right
     */
    public function __construct(Iterator $left, Iterator $right,
                                callable $keyCmpCallback = null, callable $valueEqualsCallback = null)
    {
        $this->left = $left;
        $this->right = $right;
        $this->keyCmpCallback = $keyCmpCallback;
        $this->valueEqualsCallback = $valueEqualsCallback;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->operation;
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $left = $this->left;
        $right = $this->right;
        $this->operation = null;

        if (($left->valid() || $right->valid())) {
            if (!$left->valid()) {
                // If left is exausted
                $this->operation = $this->getOperation(self::LEFT_MISSING);
                $right->next();
                return;
            }
            if (!$right->valid()) {
                // If right is exausted
                $this->operation = $this->getOperation(self::RIGHT_MISSING);
                $left->next();
                return;
            }


            // Compare items
            if ($callback = $this->keyCmpCallback) {
                $cmp = $callback($left, $right);
            } else {
                $lItem = $left->current();
                $rItem = $right->current();
                $cmp = ($lItem === $rItem ? 0 : (($lItem < $rItem) ? -1 : 1));
            }
            // Create operations
            if ($cmp < 0) {
                // If left precedes right
                $this->operation = $this->getOperation(self::RIGHT_MISSING);
                $left->next();
                return;
            }
            if ($cmp > 0) {
                // right precedes left
                $this->operation = $this->getOperation(self::LEFT_MISSING);
                $right->next();
                return;
            }
            // Equal items
            $equals = ($callback = $this->valueEqualsCallback) ? $callback($left, $right) : true;
            $this->operation = $this->getOperation($equals ? self::EQUAL : self::NOT_EQUAL);
            $left->next();
            $right->next();
        }
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return (bool) $this->operation;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->left->rewind();
        $this->right->rewind();
        $this->key = -1;
        $this->next();
    }

    /**
     * Creates a operation for sync
     * @param $operation string
     * @return array|null
     */
    protected function getOperation($operation) {
        $op = [ 'type' => $operation ];

        switch ($operation) {
            case self::LEFT_MISSING:
                $op['item'] = [
                    'key' => $this->right->key(),
                    'value' => $this->right->current()
                ];
                break;
            case self::RIGHT_MISSING:
                $op['item'] = [
                    'key' => $this->left->key(),
                    'value' => $this->left->current()
                ];
                break;
            case self::EQUAL:
            case self::NOT_EQUAL:
                $op['left'] = [
                    'key' => $this->left->key(),
                    'value' => $this->left->current()
                ];
                $op['right'] = [
                    'key' => $this->right->key(),
                    'value' => $this->right->current()
                ];
                break;
        }

        $this->key++;
        return $op;
    }
}
