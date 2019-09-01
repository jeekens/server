<?php declare(strict_types=1);


namespace Jeekens\Server;


use Jeekens\Std\Str;
use ReflectionClass;
use function array_keys;
use function array_map;
use function method_exists;
use function strtolower;
use function strtoupper;

/**
 * Class KernelAbstract
 *
 * @package Jeekens\Server
 */
abstract class KernelAbstract implements KernelInterface
{

    /**
     * @return EventHandlerIteratorInterface
     *
     * @throws \ReflectionException
     */
    public function getEventHandlerIterator(): EventHandlerIteratorInterface
    {
        $ref = new ReflectionClass(SWEvents::class);

        $allEvents = array_map(function ($const) {
            return Str::camel(strtolower($const));
        }, array_keys($ref->getConstants()));

        $eventHandlers = [];

        foreach ($allEvents as $event) {
            if (method_exists($this, $event)) {
                $eventHandlers[$ref->getconstant(strtoupper(Str::snake($event)))] = [$this, $event];
            }
        }

        return new EventHandlerIterator($eventHandlers);
    }

}