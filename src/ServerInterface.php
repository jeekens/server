<?php declare(strict_types=1);


namespace Jeekens\Server;

use Swoole\Server;

/**
 * Interface ServerInterface
 *
 * @package Jeekens\Server
 */
interface ServerInterface
{

    /**
     * 设置一个监听端口
     *
     * @param array $config
     * @param array $setting
     * @param KernelInterface $kernel
     */
    public function addListen(array $config, KernelInterface $kernel, ?array $setting = null);

    /**
     * 获取服务对象
     *
     * @return Server
     */
    public function getServer();

    /**
     * 注册事件处理
     *
     * @param KernelInterface $kernel
     */
    public function registerKernel(KernelInterface $kernel);

    /**
     * @return mixed
     */
    public function stop();

    /**
     * @return mixed
     */
    public function reload();

    /**
     * @return mixed
     */
    public function start();

    /**
     * @return bool
     */
    public function isBoot(): bool;

    /**
     * @return string|null
     */
    public function getPidFile(): ?string;

    /**
     * @param array $setting
     *
     * @return mixed
     */
    public function setSetting(array $setting);

    /**
     * @return array
     */
    public function getSetting(): ?array;

    /**
     * @param string $logfile
     */
    public function setLogFile(string $logfile);

    /**
     * @return string|null
     */
    public function getLogFile(): ?string;

    /**
     * @param string $pidFile
     */
    public function setPidFile(string $pidFile);

    /**
     * @param int $port
     */
    public function setPort(int $port);

    /**
     * @return int|null
     */
    public function getPort(): ?int;

    /**
     * @param string $host
     */
    public function setHost(string $host);

    /**
     * @return string|null
     */
    public function getHost(): ?string;

    /**
     * @param int $mode
     */
    public function setMode(int $mode);

    /**
     * @return int
     */
    public function getMode(): ?int;

    /**
     * @param int $sockType
     */
    public function setSockType(int $sockType);

    /**
     * @return int
     */
    public function getSockType(): ?int;

    /**
     * @return bool
     */
    public function isDaemon(): bool;

    /**
     * @param int $workId
     * @param bool $waitEvent
     */
    public function stopWork(int $workId = -1, bool $waitEvent = false);

    /**
     * 返回全部work进程id map
     *
     * @return int[]
     */
    public function getWorkPidMap(): array;

    /**
     * 返回master进程id
     *
     * @return int
     */
    public function getMasterPid(): int;

    /**
     * 返回manager进程id
     *
     * @return int
     */
    public function getManagerPid(): int;

    /**
     * 获取当前work进程pid
     *
     * @return int
     */
    public function getWorkPid(): int;

    /**
     * 获取work id map
     *
     * @return int[]
     */
    public function getWorkIdMap(): array;

    /**
     * 获取当前work id
     *
     * @return int
     */
    public function getWorkId(): int;

    /**
     * 返回task id map
     *
     * @return array
     */
    public function getTaskIdMap(): array;

    /**
     * 判断当前是task进程还是work进程
     * @return bool
     */
    public function isTaskOrWork(): bool;

    /**
     * 重启任务进程
     */
    public function reloadTask();

    /**
     * @param bool $daemon
     */
    public function daemon(bool $daemon);

}