<?php declare(strict_types=1);


namespace Jeekens\Server\Server;


use Jeekens\Server\Sever;
use Swoole\Http\Server;
use const SWOOLE_PROCESS;

/**
 * Class Http
 *
 * @package Jeekens\Server\Server
 */
class Http extends Sever
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