<?php

/**
 * CancelableEvent.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\components\event;
use Exception;

/**
 *
 * Base event that can be cancelled. Cancelled events will stop propagation
 * to other event handlers and
 *
 */

class CancelableEvent extends Event
{

    /** @var bool  */
    private $cancelled = false;

    /** @var mixed */
    private $cancelReason;

    /**
     * Marks the posting event as cancelled and prevents all other succeeding
     * event handlers from processing it.
     *
     * @param mixed $reason
     * @param mixed $culprit
     */
    public function cancel($reason = null)
    {
        $this->stopPropagation();
        $this->cancelled = true;
        $this->cancelReason = $reason;
    }

    /**
     * Returns whether the event has been cancelled using {@link cancel} method
     * or not.
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }

    /**
     * Returns the reason given when the cancel method is called
     *
     * @return mixed
     *
     * @deprecated Use reasonForCancellation() instead
     */
    public function getReasonForCancellation()
    {
        return $this->cancelReason;
    }

    /**
     * Returns the reason given when the cancel method is called
     *
     * @return Exception
     */
    public function reasonForCancellation()
    {
        return $this->cancelReason;
    }

}
