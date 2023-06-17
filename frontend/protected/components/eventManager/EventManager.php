<?php

/**
 * EventManager.php
 *
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

namespace SegHis\components\eventManager;

use Segworks\Base\PriorityQueue;
use Segworks\EventManager\EventInterface;
use Segworks\EventManager\EventServiceInterface;
use Segworks\EventManager\ResponseCollection;
use Segworks\EventManager\SubscriberInterface;

/**
 * Description
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @package application.components
 */

class EventManager
    extends \CApplicationComponent
    implements EventServiceInterface
{
    /**
     * @var PriorityQueue[] List of events with their corresponding callbacks
     */
    protected $events = array();
    /**
     * @var array Caches metaclass information
     */
    private $meta = array();

    /**
     * @see CApplicationComponent::init
     */
    public function init()
    {
        parent::init();

        /** @var \CWebApplication $app */
        $app = \Yii::app();
        $modules = $app->getModules();
        foreach ($modules as $module => $config) {
            /**
             * Using Yii::getPathOfAlias() has the undesired side effect that
             * causes Yii to instantiate the module if a module is passed as
             * the root alias. For example, this will cause the `gii` module
             * to load if the alias contains `gii` as the root alias and
             * subsequently reset the `user` component when the gii module
             * is instantiated.
             *
             * We fall back to resolving the path alias manually to avoid this.
             */
            $pathTokens = array(
                \Yii::app()->basePath,
                'modules',
                $module,
                'config',
                'subscribers'
            );
            $path = implode(DIRECTORY_SEPARATOR, $pathTokens) . '.php';
            if (is_file($path)) {
                $collection = include_once $path;
                if ($collection) {
                    $this->subscribeCollection($collection);
                }
            }
        }
    }

    /**
     * @see EventServiceInterface::attachListener
     */
    public function attachListener($eventName, $callback, $priority=1)
    {
        if (!isset($this->events[$eventName])) {
            $this->events[$eventName] = new PriorityQueue();
        }
        $this->events[$eventName]->insert($callback, $priority);
    }

    /**
     * @see EventServiceInterface::detachListener
     */
    public function detachListener($eventName, $callback)
    {
        if (isset($this->events[$eventName])) {
            $this->events[$eventName]->remove($callback);
        }
    }

    /**
     * @see EventServiceInterface::publish
     */
    public function publish(EventInterface $event, $callback = null)
    {
        $eventName = $event->getName();
        if (!isset($this->events[$eventName])) {
            return null;
        }
        return $this->triggerListeners($this->getListeners($eventName), $event, $callback);
    }

    /**
     * @see EventServiceInterface::subscribe
     * @todo Implement multisubscription
     */
    public function subscribe(SubscriberInterface $subscriber)
    {
        $listeners = $subscriber->getListeners();
        foreach ($listeners as $listener) {
            // Normalize
            $listener = (array) $listener + array('event' => null, 'callback' => null, 'priority' => 1);
            if (empty($listener['event'])) {
                throw new \CException('Event name not specified');
            }
            if (!is_callable($listener['callback'])) {
                throw new \CException('Invalid callback specified');
            }
            $this->attachListener($listener['event'], $listener['callback'], $listener['priority']);
        }
    }

    /**
     * @see EventServiceInterface::unsubscribe
     */
    public function unsubscribe(SubscriberInterface $subscriber)
    {
        $listeners = $subscriber->getListeners();
        foreach ($listeners as $listener) {
            // Normalize
            $listener = (array) $listener + array('event' => null, 'callback' => null, 'priority' => 1);
            $this->detachListener($listener['event'], $listener['callback']);
        }
    }

    /**
     *
     * @param array|SubscriberCollection $collection
     * @throws \CException if the collection conifuration contains an invalid
     * subscriber object
     */
    public function subscribeCollection($collection)
    {
        if (is_array($collection)) {
            $subscribers = $collection;
        } elseif ($collection instanceof SubscriberCollection) {
            $subscribers = $collection->getSuscribers();
        } else {
            throw new \CException('Invalid subscriber collection');
        }

        foreach ($subscribers as $subscriber) {
            // Normalize subscriber collection item parameters
            if (is_string($subscriber)) {
                $subscriber = array('class' => $subscriber);
            }
            if (is_array($subscriber)) {
                $subscriber = \Yii::createComponent($subscriber);
            }
            if (!$subscriber instanceof SubscriberInterface) {
                throw new \CException('Invalid subscriber specified');
            }

            $this->subscribe($subscriber);
        }
    }

    /**
     * @param array|SubscriberCollection $collection
     */
    public function unsubscribeCollection($collection)
    {
        // @todo Implement unsubscribeCollection() method.
    }

    /**
     * @see EventServiceInterface::getListeners
     */
    public function getListeners($eventName = null)
    {

        if (null === $eventName) {
            return $this->events;
        }
        return array_filter($this->events[$eventName]->toArray());
    }

    /**
     * Notifies the listeners of an event.
     *
     * This method can be overridden to add functionality that is executed
     * for each listener.
     *
     * @param callable[] $listeners List of listeners to be notified
     * @param EventInterface $event The event object to pass to the subscribers
     * @param null|callable $callback The callback function which evaluates listener responses
     * @return ResponseCollection
     */
    protected function triggerListeners($listeners, EventInterface $event, $callback = null)
    {
        $responses = new ResponseCollection;
        foreach ($listeners as $listener) {
            $responses->push(call_user_func($listener, $event));
            if ($event->isPropagationStopped()) {
                $responses->setStopped(true);
                break;
            }

            // If the result causes our validation callback to return false,
            // stop propagation
            if ($callback && call_user_func($callback, $responses->last())) {
                $responses->setStopped(true);
                break;
            }
        }
        return $responses;
    }

    /**
     *
     * @param string $parent
     * @param string $child
     * @return bool
     */
    private function isSubclassOf($parent, $child)
    {
        if ($child === $parent)
            return true;

//        return is_subclass_of($eventName, $event);
        /**
         * Do we even need to cache?
         */
        if (!isset($this->meta[$parent])) {
            $this->meta[$parent] = array();
            $this->meta[$parent]['whitelist'] = array();
            $this->meta[$parent]['blacklist'] = array();
        }

        if (isset($this->meta[$parent]['whitelist'][$child]))
            return true;
        if (isset($this->meta[$parent]['blacklist'][$child]))
            return false;

        if (is_subclass_of($parent, $child)) {
            $this->meta[$parent]['whitelist'][$child] = true;
            return true;
        } else {
            $this->meta[$parent]['blacklist'][$child] = true;
            return false;
        }

    }

}