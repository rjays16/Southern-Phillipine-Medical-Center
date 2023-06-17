<?php

/**
 * ListenerProvider.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\modules\jnj\events;
use League\Event\CallbackListener;
use League\Event\ListenerAcceptorInterface;
use League\Event\ListenerProviderInterface;
use SegHEIRS\components\event\EventNames;
use SegHEIRS\modules\integrations\modules\jnj\events\handlers\AckHl7MessageHandler;
use SegHEIRS\modules\integrations\modules\jnj\events\handlers\CbgOrderedMessageHandler;


/**
 *
 * Description of ListenerProvider
 *
 */

class ListenerProvider implements ListenerProviderInterface
{

    /**
     * @param ListenerAcceptorInterface $acceptor
     *
     * @return void
     */
    public function provideListeners(ListenerAcceptorInterface $acceptor)
    {              
        $acceptor->addListener(
            EventNames::EVENT_HL7_ACKNOWLEDGEMENT,
            CallbackListener::fromCallable(array(new AckHl7MessageHandler(), 'handle'))
        );        

        $acceptor->addListener(
            EventNames::EVENT_CBG_ORDERED,
            CallbackListener::fromCallable(array(new CbgOrderedMessageHandler(), 'handle'))
        );
    }
}