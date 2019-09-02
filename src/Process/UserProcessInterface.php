<?php declare(strict_types=1);


namespace Jeekens\Server\Process;


use Swoole\Process;
use Swoole\Server;

interface UserProcessInterface
{

    public function name(): string;

    public function redirectStdinStdout() : bool;

    public function pipeType(): int;

    public function enableCoroutine(): bool;

    public function handle(Server $server, Process $process);

}