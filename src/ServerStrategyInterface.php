<?php declare(strict_types=1);


namespace Jeekens\Server;


interface ServerStrategyInterface
{

    /**
     * 注册一个服务
     *
     * @param string $name
     * @param string $class
     */
    public function registerServerClass(string $name, string $class);

    /**
     * 取消一个服务
     *
     * @param string $name
     */
    public function cancelServerClass(string $name);

    /**
     * 根据配置实例化对应服务
     *
     * @param array $configure
     * @param string $pidFile
     * @param string $logFile
     * @param bool $daemon
     *
     * @return ServerInterface
     */
    public function getServer(array $configure, string $pidFile, string $logFile, bool $daemon = false): ServerInterface;

    /**
     * 获取服务类名
     *
     * @param string $type
     *
     * @return string|null
     */
    public function getServerClassName(string $type): ?string;

    /**
     * 获取服务默认配置
     *
     * @param string|null $type
     *
     * @return array
     */
    public function getDefaultConf(?string $type = null): array;

    /**
     * 注册一个服务默认配置
     *
     * @param string $type
     * @param array $conf
     *
     * @return mixed
     */
    public function registerDefaultConf(string $type, array $conf);

}