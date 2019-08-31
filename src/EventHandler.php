<?php declare(strict_types=1);


namespace Jeekens\Server;

/**
 * Class EventHandler
 *
 * @package Jeekens\Server
 */
class EventHandler implements EventHandlerInterface
{

    /**
     * @var string
     */
    protected $eventName;

    /**
     * @var callable
     */
    protected $handler;

    /**
     * EventHandler constructor.
     *
     * @param string $name
     * @param callable $callable
     */
    public function __construct(string $name, callable $callable)
    {
        $this->eventName = $name;
        $this->handler = $callable;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @return callable
     */
    public function getHandler(): callable
    {
        return $this->handler;
    }

}