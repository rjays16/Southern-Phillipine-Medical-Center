<?php

namespace spec\League\Event;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use League\Event\ListenerInterface;
use League\Event\EventAbstract;
use League\Event\Emitter;

class OneTimeListenerSpec extends ObjectBehavior
{
    protected $listener;

    function let(ListenerInterface $listener)
    {
        $this->listener = $listener;
        $this->beConstructedWith($this->listener);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('League\Event\OneTimeListener');
    }

    function it_should_expose_the_wrapped_listener()
    {
        $this->getWrappedListener()->shouldHaveType('League\Event\ListenerInterface');
    }

    function it_should_unregister_and_forward_the_handle_call(EventAbstract $event, Emitter $emitter)
    {
        $event->getName()->willReturn('event');
        $event->getEmitter()->willReturn($emitter);
        $emitter->removeListener('event', $this->listener)->shouldBeCalled();
        $this->listener->handle($event)->shouldBeCalled();
        $this->handle($event);
    }

    function it_should_identify_itself()
    {
        $this->listener->isListener($this->listener)->willReturn(true);
        $this->isListener($this)->shouldReturn(true);
        $this->isListener($this->listener)->shouldReturn(true);
    }
}
