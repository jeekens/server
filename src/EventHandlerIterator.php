<?php declare(strict_types=1);


namespace Jeekens\Server;

use function array_key_exists;

/**
 * Class EventHandlerIterator
 *
 * @package Jeekens\Server
 */
class EventHandlerIterator implements EventHandlerIteratorInterface
{

    /**
     * @var int
     */
    protected $key = 0;

    /**
     * @var array
     */
    protected $eventHandlers = [];

    /**
     * EventHandlerIterator constructor.
     *
     * @param array $eventListeners
     */
    public function __construct(array $eventListeners = [])
    {
        foreach ($eventListeners as $name => $listener) {
            $this->eventHandlers[] = new EventHandler($name, $listener);
        }
    }

    /**
     * @return EventHandlerInterface|null
     */
    public function current(): ?EventHandlerInterface
    {
        return $this->eventHandlers[$this->key()] ?? null;
    }

    /**
     * @return int|mixed
     */
    public function key()
    {
        return $this->key;
    }

    public function next()
    {
        $this->key++;
    }

    public function rewind()
    {
        $this->key = 0;
    }

    public function valid()
    {
        return array_key_exists($this->key, $this->eventHandlers);
    }

}