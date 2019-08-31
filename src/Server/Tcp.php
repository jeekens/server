<?php declare(strict_types=1);


namespace Jeekens\Server\Server;


use Jeekens\Server\SeverAbstract;
use Swoole\Server;
use const SWOOLE_PROCESS;
use const SWOOLE_SOCK_TCP;

/**
 * Class Tcp
 *
 * @package Jeekens\Server\Server
 */
class Tcp extends SeverAbstract
{

    protected $host = '0.0.0.0';

    protected $port = 0;

    protected $mode = SWOOLE_PROCESS;

    protected $sockType = SWOOLE_SOCK_TCP;


    protected function getServerClass(): string
    {
        return Server::class;
    }

}