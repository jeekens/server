<?php


namespace Jeekens\Server;


use Swoole\Server;

interface ServerAdapterInterface
{

    public static function setServer(Server $server);

    public static function getServer(): Server;

    public function start(bool $daemon = false);

    public function restart();

    public function stop();

    public function shutdown();

}