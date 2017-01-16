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

use IteratorIterator;
use Traversable;

/**
 * A Iterator that applies the callback to each element accessed
 * in the inner iterator.
 *
 * @package \TTIC\Commons\Iterator
 * @author Marcos Lois Bermúdez <marcos.lois@teratic.com>
 */
class CallbackMapIterator extends IteratorIterator
{
    /** @var callable */
    private $callback;

    private $current;

    /**
     * Create CallbackMapIterator from anything that is traversable
     *
     * @param \Traversable $iterator
     * @since 5.1.0
     */
    public function __construct(Traversable $iterator, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException("The supplied callback is not callable.");
        }

        parent::__construct($iterator);
        $this->callback = $callback;
    }

    /**
     * Get the current value after calling the registered callback.
     * The callback will only be fired once on first access to current value.
     *
     * @return mixed The value of the current element.
     */
    public function current()
    {
        // Avoid call the callback more than once
        if (!$this->current && parent::valid()) {
            $this->current = call_user_func($this->callback, parent::current(), parent::key(), $this);
        }

        return $this->current;
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        $this->current = null;
        parent::next();
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        $this->current = null;
        parent::rewind();
    }
}
