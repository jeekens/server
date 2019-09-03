<?php declare(strict_types=1);


namespace Jeekens\Server;


use function method_exists;
use function var_dump;

/**
 * Class KernelAbstract
 *
 * @package Jeekens\Server
 */
abstract class KernelAbstract implements KernelInterface
{

    protected $allEvent = [
//        SWEvents::ON_BUFFER_EMPTY,
//        SWEvents::ON_BUFFER_FULL,
        SWEvents::ON_CLOSE,
        SWEvents::ON_CONNECT,
        SWEvents::ON_FINISH,
        SWEvents::ON_HAND_SHAKE,
        SWEvents::ON_MANAGER_START,
        SWEvents::ON_MANAGER_STOP,
        SWEvents::ON_MESSAGE,
        SWEvents::ON_OPEN,
        SWEvents::ON_PACKET,
        SWEvents::ON_WORKER_STOP,
        SWEvents::ON_WORKER_START,
        SWEvents::ON_WORKER_EXIT,
        SWEvents::ON_WORKER_ERROR,
        SWEvents::ON_TIMER,
        SWEvents::ON_TASK,
        SWEvents::ON_START,
        SWEvents::ON_SHUTDOWN,
        SWEvents::ON_REQUEST,
        SWEvents::ON_RECEIVE,
        SWEvents::ON_PIPE_MESSAGE,
    ];


    /**
     * @return EventHandlerIteratorInterface
     */
    public function getEventHandlerIterator(): EventHandlerIteratorInterface
    {
        $eventHandlers = [];

        foreach ($this->allEvent as $event) {
            $method = 'on'.$event;
            if (method_exists($this, $method)) {
                $eventHandlers[$event] = [$this, $method];
            }
        }

        return new EventHandlerIterator($eventHandlers);
    }

}