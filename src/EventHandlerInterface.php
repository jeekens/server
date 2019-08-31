<?php declare(strict_types=1);


namespace Jeekens\Server;

/**
 * Interface EventHandlerInterface
 * @package Jeekens\Server
 */
interface EventHandlerInterface
{

    /**
     * @return string
     */
    public function getEventName(): string;

    /**
     * @return callable
     */
    public function getHandler(): callable;

}