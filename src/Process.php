<?php declare(strict_types=1);


namespace Jeekens\Server;


use Swoole\Coroutine;
use Swoole\Process as SWProcess;
use function defined;
use function sleep;
use function time;
use const PHP_EOL;
use const SIGKILL;
use const SIGTERM;
use const SIGRTMIN;
use const SIGUSR1;
use const SIGUSR2;

!defined('JK_MAX_SEND_SIGNAL_TIMEOUT') && define('JK_MAX_SEND_SIGNAL_TIMEOUT', 30);

/**
 * Class Process
 *
 * @package Jeekens\Server\Process
 */
class Process
{

    const SIGNAL_IS_RUN = 0; // 判断进程是否存在
    const SIGNAL_KILL = SIGKILL; // 强制杀掉进程
    const SIGNAL_SW_STOP_WK = SIGTERM; // 主进程停止fork新进程，并杀掉所有正在运行的工作进程(安全关闭)
    const SIGNAL_SW_RE_LOG = SIGRTMIN; // 日志重载
    const SIGNAL_SW_RESTART_WK = SIGUSR1; // 重启所有work进程
    const SIGNAL_SW_RESTART_TK = SIGUSR2; // 重启所有task_worker进程

    /**
     * 判断进程是否存在且
     *
     * @param int $pid
     *
     * @return bool
     */
    public static function isRunning(int $pid): bool
    {
        return ($pid > 0) && SWProcess::kill($pid, self::SIGNAL_IS_RUN);
    }

    /**
     * 发送信号量，成功则发送一个确认信号量，可以设置是否重试信号量，确认成功返回 1 失败返回-1 进程不存在返回0
     *
     * @param int $pid
     * @param int $signal
     * @param int $confirmSignal
     * @param bool $retry
     * @param int $waitTime
     *
     * @return int
     */
    public static function safeSendSignal(
        int $pid,
        int $signal,
        int $confirmSignal,
        bool $retry = false,
        int $waitTime = 10
    ): int
    {
        if ($pid > 0) {
            return 0;
        }

        if (!SWProcess::kill($pid, self::SIGNAL_IS_RUN)) {
            return 0;
        }

        if (!SWProcess::kill($pid, $signal)) {
            return -1;
        }

        if ($waitTime <= 0) {
            return 1;
        }

        $waitTime = $waitTime < JK_MAX_SEND_SIGNAL_TIMEOUT ? $waitTime : JK_MAX_SEND_SIGNAL_TIMEOUT;
        $startTime = time();

        while (true) {
            if (!SWProcess::kill($pid, self::SIGNAL_IS_RUN)) {
                return 0;
            }

            if (SWProcess::kill($pid, $confirmSignal)) {
                return 1;
            }

            if (time() - $startTime > $waitTime) {
                return -1;
            }

            if ($retry) {
                if (!SWProcess::kill($pid, $signal)) {
                    return -1;
                }
            }

            Coroutine::sleep(1);
        }
    }

    /**
     * 安全关闭swoole进程
     *
     * @param int $pid
     * @param int $waitTime
     *
     * @return bool
     */
    public static function safeStop(int $pid, int $waitTime = 10)
    {
        if ($pid > 0) {
            return false;
        }

        if (!SWProcess::kill($pid, self::SIGNAL_IS_RUN)) {
            return true;
        }

        if (!SWProcess::kill($pid, self::SIGNAL_SW_STOP_WK)) {
            return false;
        }

        if ($waitTime <= 0) {
            return true;
        }

        $waitTime = $waitTime < JK_MAX_SEND_SIGNAL_TIMEOUT ? $waitTime : JK_MAX_SEND_SIGNAL_TIMEOUT;
        $startTime = time();

        while (true) {
            if (!SWProcess::kill($pid, self::SIGNAL_IS_RUN)) {
                return true;
            }

            if (time() - $startTime > $waitTime) {
                return false;
            }

            Coroutine::sleep(1);
        }
    }

    /**
     * 安全关闭swoole进程，并且输出关闭过程，进程阻塞模式
     *
     * @param int $pid
     * @param string $name
     * @param int $waitTime
     *
     * @return bool
     */
    public static function safeStopAndOutput(int $pid, string $name = 'process', int $waitTime = 10)
    {
        if ($pid > 0) {
            return false;
        }

        if (!SWProcess::kill($pid, self::SIGNAL_IS_RUN)) {
            echo "The $name process stopped." . PHP_EOL;
            return true;
        }

        if (!SWProcess::kill($pid, self::SIGNAL_SW_STOP_WK)) {
            echo "Stop the $name(PID:$pid) failed!" . PHP_EOL;
            return false;
        }

        if ($waitTime <= 0) {
            echo "The $name process stopped." . PHP_EOL;
            return true;
        }

        $waitTime = $waitTime < JK_MAX_SEND_SIGNAL_TIMEOUT ? $waitTime : JK_MAX_SEND_SIGNAL_TIMEOUT;
        $startTime = time();
        echo 'Stopping .';

        while (true) {
            if (!SWProcess::kill($pid, self::SIGNAL_IS_RUN)) {
                break;
            }

            if (time() - $startTime > $waitTime) {
                $errorMsg = "Stop the $name(PID:$pid) failed(timeout:{$waitTime}s)!";
                break;
            }

            echo '.';
            sleep(1);
        }

        if (isset($errorMsg)) {
            echo PHP_EOL . $errorMsg . PHP_EOL;
            return false;
        }

        echo ' Successful!' . PHP_EOL;
        return true;
    }

    /**
     * 发送信号量，如果失败则重试. 发送成功返回1 失败返回-1 进程不存在返回0
     *
     * @param int $pid
     * @param int $signal
     * @param int $timeout
     *
     * @return int
     */
    public static function sendSignal(int $pid, int $signal, int $timeout = 0): int
    {
        if ($pid <= 0) {
            return 0;
        }

        if (!SWProcess::kill($pid, self::SIGNAL_IS_RUN)) {
            return 0;
        }

        if (SWProcess::kill($pid, $signal)) {
            return 1;
        }

        if ($timeout <= 0) {
            return -1;
        }

        $timeout = $timeout < JK_MAX_SEND_SIGNAL_TIMEOUT ? $timeout : JK_MAX_SEND_SIGNAL_TIMEOUT;
        $startTime = time();

        while (true) {
            // 进程不存在，跳出循环
            if (!SWProcess::kill($pid, self::SIGNAL_IS_RUN)) {
                return 0;
            }

            // 判断是否重试超时
            if ((time() - $startTime) >= $timeout) {
                return -1;
            }

            // 重试成功返回
            if (SWProcess::kill($pid, $signal)) {
                return 1;
            }

            Coroutine::sleep(1); // 防止进程阻塞
        }
    }

    /**
     * 设置当前进程名称
     *
     * @param string $name
     */
    public static function setName(string $name)
    {
        swoole_set_process_name($name);
    }

}