<?php declare(strict_types=1);


namespace Jeekens\Server\Server;


use Jeekens\Server\Sever;
use Swoole\Server;
use const SWOOLE_PROCESS;
use const SWOOLE_SOCK_UDP;

/**
 * Class Udp
 *
 * @package Jeekens\Server\Server
 */
class Udp extends Sever
{

    protected $host = '0.0.0.0';

    protected $port = 0;

    protected $mode = SWOOLE_PROCESS;

    protected $sockType = SWOOLE_SOCK_UDP;


    protected function getServerClass(): string
    {
        return Server::class;
    }

}