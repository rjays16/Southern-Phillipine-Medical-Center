<?php

namespace League\Event;

class CallbackListener implements ListenerInterface
{
    /**
     * @var  callable  $callback
     */
    protected $callback;

    /**
     * Constructor
     *
     * @param  callable  $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Get the callback
     *
     * @return  callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * {inheritdoc}
     */
    public function handle(EventAbstract $event)
    {
        call_user_func_array($this->callback, func_get_args());
    }

    /**
     * {inheritdoc}
     */
    public function isListener($listener)
    {
        if ($listener instanceof CallbackListener) {
            $listener = $listener->getCallback();
        }

        return $this->callback === $listener;
    }
}
