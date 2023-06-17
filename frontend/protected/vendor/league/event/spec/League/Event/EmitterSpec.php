<?php

namespace spec\League\Event;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Closure;
use League\Event\Event;
use League\Event\ListenerInterface;

class EmitterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('League\Event\Emitter');
    }

    function it_should_accept_listeners()
    {
        $this->addListener('event', function () {})
            ->shouldReturn($this);

        $this->hasListeners('event')->shouldReturn(true);
    }

    function it_should_allow_you_to_remove_listeners()
    {
        $callback = function () {};
        $this->addListener('event', $callback);
        $this->removeListener('event', $callback);
        $this->shouldNotHaveListeners('event');
    }

    function it_should_allow_you_to_remove_multiple_listeners_at_once()
    {
        $callback = function () {};
        $this->addListener('event', $callback);
        $this->addListener('event', $callback);
        $this->removeAllListeners('event');
        $this->hasListeners('event')->shouldReturn(false);
    }

    function it_should_throw_an_exception_when_registering_an_invalid_listener()
    {
        $this->shouldThrow('InvalidArgumentException')
            ->duringAddListener('event', 'invalid@callback');
    }

    function it_should_return_an_empty_array_when_an_event_has_no_listeners()
    {
        $this->getListeners('event')->shouldReturn([]);
    }

    function it_should_allow_you_to_emit_plain_events()
    {
        $callback = function () {};
        $this->addListener('event', $callback);
        $this->emit('event')->shouldReturnAnInstanceOf('League\Event\Event');
    }

    function it_should_allow_you_to_emit_custom_events(Event $event)
    {
        $event->setEmitter($this)->shouldBeCalled();
        $event->getName()->willReturn('event');
        $event->isPropagationStopped()->willReturn(false);
        $callback = function ($event) {};
        $this->addListener('event', $callback);
        $this->emit($event)->shouldReturnAnInstanceOf('League\Event\Event');
    }

    function it_should_stop_respond_to_stopping_propagation(Event $event)
    {
        $event->setEmitter($this)->shouldBeCalled();
        $event->getName()->willReturn('event');
        $event->isPropagationStopped()->willReturn(true);
        $callback = function ($event) {};
        $this->addListener('event', $callback);
        $this->emit($event)->shouldReturnAnInstanceOf('League\Event\Event');
    }

    function it_should_throw_an_exception_when_an_invalid_event_type_is_supplied()
    {
        $this->shouldThrow('InvalidArgumentException')
            ->duringEmit(true);
    }

    function it_should_accept_custom_listeners(ListenerInterface $listener)
    {
        $this->addListener('event', $listener);
        $this->getListeners('event')->shouldContain($listener);
    }

    function it_should_convert_listeners_to_one_time_listeners(ListenerInterface $listener)
    {
        $this->addOneTimeListener('event', $listener);
        $this->getListeners('event')->shouldContainInstanceOfType('League\Event\OneTimeListener');
        $this->getListeners('event')->shouldNotContain($listener);
    }

    public function getMatchers()
    {
        return [
            'containInstanceOfType' => function ($subject, $interface) {
                foreach ($subject as $value) {
                    if ($value instanceof $interface) {
                        return true;
                    }
                }

                return false;
            },
        ];
    }
}
