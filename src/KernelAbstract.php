<?php declare(strict_types=1);


namespace Jeekens\Server;


use Jeekens\Std\Str;
use ReflectionClass;
use function array_keys;
use function array_map;
use function in_array;
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
        $methods = get_class_methods($this);

        $ref = new ReflectionClass(SWEvents::class);

        $allEvents = array_map(function ($const) {
            return Str::camel(strtolower($const));
        }, array_keys($ref->getConstants()));

        $eventHandlers = [];

        foreach ($methods as $method) {
            if (in_array($method, $allEvents)) {
                $eventHandlers[$ref->getconstant(strtoupper($method))] = [$this, $method];
            }
        }

        return new EventHandlerIterator($eventHandlers);
    }

}