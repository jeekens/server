<?php declare(strict_types=1);


namespace Jeekens\Server\Process;


use const SOCK_DGRAM;

/**
 * Class JKProcess
 *
 * @package Jeekens\Server\Process
 */
class JKProcess
{


    public function __construct(
        callable $function,
        bool $redirect_stdin_stdout = false,
        int $pipe_type = SOCK_DGRAM,
        bool $enable_coroutine = false
    )
    {

    }

}