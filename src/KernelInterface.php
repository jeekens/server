<?php


namespace Jeekens\Server;


interface KernelInterface
{

    public function getEventHandlerIterator(): ?EventHandlerIteratorInterface;

}