<?php declare(strict_types=1);


namespace Jeekens\Server;


use Iterator;

interface EventHandlerIteratorInterface extends Iterator
{

    public function current() : ?EventHandlerInterface;

}