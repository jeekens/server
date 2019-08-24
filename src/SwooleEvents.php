<?php declare(strict_types=1);


namespace Jeekens\Server;


 final class SwooleEvents
{

    const ON_START = 'start';

    const ON_SHUTDOWN = 'shutdown';

    const ON_WORKER_START = 'workerStart';

    const ON_WORKER_STOP = 'workerStop';

    const ON_WORKER_EXIT = 'workerExit';

    const ON_TIMER = 'timer';

    const ON_CONNECT = 'connect';

    const ON_RECEIVE = 'receive';

    const ON_PACKET = 'packet';

    const ON_CLOSE = 'close';

    const ON_BUFFER_FULL = 'bufferFull';

    const ON_BUFFER_EMPTY = 'bufferEmpty';

    const ON_TASK = 'task';

    const ON_FINISH = 'finish';

    const ON_PIPE_MESSAGE = 'pipeMessage';

    const ON_WORKER_ERROR = 'workerError';

    const ON_MANAGER_START = 'managerStart';

    const ON_MANAGER_STOP = 'managerStop';

    const ON_REQUEST = 'request';

    const ON_HAND_SHAKE = 'handShake';

    const ON_MESSAGE = 'message';

    const ON_OPEN = 'open';

}