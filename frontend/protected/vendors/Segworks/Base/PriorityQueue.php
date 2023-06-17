<?php
/**
 * PriorityQueue.php
 *
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

namespace Segworks\Base;

use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Extension of SplPriorityQueue with added functionality like predictable heap
 * order for items with the same priority, remove method and allowing multiple
 * iterations over the queue items.
 *
 * Based on {@link https://mwop.net/blog/253-Taming-SplPriorityQueue.html},
 * {@link https://gist.github.com/denisdeejay/1ee0ce70b3afe76cf31e} and
 * ZF2's SplPriorityQueue implementation.
 *
 * @author Alvin Quinones
 */
class PriorityQueue implements Countable, IteratorAggregate
{

    /**
     *
     */
    public function __construct()
    {
        $this->innerQueue = new SplPriorityQueue;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->innerQueue);
    }

    /**
     * @param $datum
     * @param $priority
     */
    public function insert($datum, $priority)
    {
        $this->innerQueue->insert($datum, $priority);
    }

    /**
     * @param $datum
     */
    public function remove($datum)
    {
        $this->innerQueue->remove($datum);
    }

    /**
     *
     * @return array
     */
    public function toArray()
    {
        return $this->innerQueue->toArray();
    }

    /**
     *
     * @return SplPriorityQueue|Traversable
     */
    public function getIterator()
    {
        return clone $this->innerQueue;
    }

}

/**
 * Extension of SplPriorityQueue with predictable heap ordering for items with
 * the same priority.
 *
 * @package Segworks\Base
 */
class SplPriorityQueue extends \SplPriorityQueue
{
    /**
     * @var int Seed used to ensure queue order for items of the same priority
     */
    protected $queueOrder = PHP_INT_MAX;

    /**
     * Insert a value with a given priority
     *
     * Utilizes {@var $serial} to ensure that values of equal priority are
     * emitted in the same order in which they are inserted.
     *
     * @param  mixed $datum
     * @param  mixed $priority
     * @return void
     */
    public function insert($datum, $priority)
    {
        if (!is_array($priority)) {
            $priority = array($priority, $this->queueOrder--);
        }
        parent::insert($datum, $priority);
    }

    /**
     * Serialize to an array
     *
     * Array will be priority => data pairs
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        foreach (clone $this as $item) {
            $array[] = $item;
        }
        return $array;
    }

    /**
     *
     * @param mixed $item
     */
    public function remove($item)
    {
        $recoverList = array();
        $this->setExtractFlags(self::EXTR_BOTH);

        foreach ($this as $em) {
            if ($em['data'] !== $item) {
                $recoverList[] = $em;
            }
        }
        $this->setExtractFlags(self::EXTR_DATA);
        foreach($recoverList as $em){
            $this->insert($em['data'], $em['priority']);
        }
    }
}
