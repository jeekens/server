<?php declare(strict_types=1);


namespace Jeekens\Server;


use Exception;
use InvalidArgumentException;
use Jeekens\Server\Server\Dgram;
use Jeekens\Server\Server\Http;
use Jeekens\Server\Server\Stream;
use Jeekens\Server\Server\Tcp;
use Jeekens\Server\Server\Udp;
use Jeekens\Server\Server\WebSocket;
use Jeekens\Std\Arr;
use Throwable;
use function array_column;
use function array_merge;
use function array_search;
use function count;
use function sprintf;
use const SWOOLE_PROCESS;
use const SWOOLE_SOCK_TCP;
use const SWOOLE_SOCK_UDP;
use const SWOOLE_SOCK_UNIX_DGRAM;
use const SWOOLE_SOCK_UNIX_STREAM;

/**
 * Class ServerStrategy
 *
 * @package Jeekens\Server
 */
final class ServerStrategy implements ServerStrategyInterface
{

    const TYPE_WEBSOCKET = 'websocket';
    const TYPE_HTTP = 'http';
    const TYPE_UDP = 'udp';
    const TYPE_TCP = 'tcp';
    const TYPE_DGRAM = 'dgram';
    const TYPE_STREAM = 'stream';

    /**
     * @var array
     */
    protected $severClass = [
        self::TYPE_WEBSOCKET => WebSocket::class,
        self::TYPE_HTTP => Http::class,
        self::TYPE_UDP => Udp::class,
        self::TYPE_TCP => Tcp::class,
        self::TYPE_DGRAM => Dgram::class,
        self::TYPE_STREAM => Stream::class,
    ];

    /**
     * @var array
     */
    protected $serverDefault = [
        self::TYPE_WEBSOCKET => [
            'host' => '0.0.0.0',
            'port' => 0,
            'mode' => SWOOLE_PROCESS,
            'sock_type' => SWOOLE_SOCK_TCP,
        ],
        self::TYPE_HTTP => [
            'host' => '0.0.0.0',
            'port' => 0,
            'mode' => SWOOLE_PROCESS,
            'sock_type' => SWOOLE_SOCK_TCP,
        ],
        self::TYPE_UDP => [
            'host' => '0.0.0.0',
            'port' => 0,
            'mode' => SWOOLE_PROCESS,
            'sock_type' => SWOOLE_SOCK_UDP,
        ],
        self::TYPE_TCP => [
            'host' => '0.0.0.0',
            'port' => 0,
            'mode' => SWOOLE_PROCESS,
            'sock_type' => SWOOLE_SOCK_TCP,
        ],
        self::TYPE_DGRAM => [
            'host' => '0.0.0.0',
            'port' => 0,
            'mode' => SWOOLE_PROCESS,
            'sock_type' => SWOOLE_SOCK_UNIX_DGRAM,
        ],
        self::TYPE_STREAM => [
            'host' => '0.0.0.0',
            'port' => 0,
            'mode' => SWOOLE_PROCESS,
            'sock_type' => SWOOLE_SOCK_UNIX_STREAM,
        ],
    ];

    /**
     * @var array
     */
    protected $customServerClass = [];

    /**
     * @param string $name
     * @param string $class
     *
     * @return $this
     */
    public function registerServerClass(string $name, string $class)
    {
        $this->customServerClass[$name] = $class;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function cancelServerClass(string $name)
    {
        if (isset($this->customServerClass[$name])) {
            unset($this->customServerClass[$name]);
        }

        return $this;
    }

    /**
     * @param array $configure
     * @param string $pidFile
     * @param string $logFile
     * @param bool $daemon
     *
     * @return ServerInterface
     */
    public function getServer(array $configure, string $pidFile, string $logFile, bool $daemon = false): ServerInterface
    {

        if (count($configure) === 1) {
            $configure = $configure[0];
            $server = $this->createSingleServer($configure, $kernelClass);
        } else {
            $server = $this->createMultipleMixServer($configure,$kernelClass);
        }

        $server->daemon($daemon);
        $server->setLogFile($logFile);
        $server->setPidFile($pidFile);

        $server->registerKernel(
            $this->getKernel($kernelClass)
        );

        return $server;
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    public function getServerClassName(string $type): ?string
    {
        if (isset($this->customServerClass[$type])) {
            return $this->customServerClass[$type];
        }

        if (isset($this->severClass[$type])) {
            return $this->severClass[$type];
        }

        return null;
    }

    /**
     * @param string $kernelClass
     *
     * @return KernelInterface
     */
    protected function getKernel(string $kernelClass): KernelInterface
    {
        try {
            $kernel = new $kernelClass();

            if (!($kernel instanceof KernelInterface)) {
                throw new Exception();
            }

        } catch (Throwable $e) {
            throw new InvalidArgumentException(sprintf(
                'Kernel must be an instantiatable "%s" interface class name.',
                KernelInterface::class
            ));
        }

        return $kernel;
    }

    /**
     * 获取默认配置
     *
     * @param string|null $type
     *
     * @return array
     */
    public function getDefaultConf(?string $type = null): array
    {
        if (empty($type)) {
            return $this->serverDefault;
        }

        return $this->serverDefault[$type] ?? [];
    }


    public function registerDefaultConf(string $type, array $conf)
    {
        $this->serverDefault[$type] = $conf;

        return $this;
    }

    /**
     * 根据配置创建服务器
     *
     * @param array $configure
     * @param string $kernelClass
     *
     * @return ServerInterface
     */
    protected function createServer(array $configure)
    {
        /**
         * @var $type string
         */
        $type = Arr::pull($configure, 'type');

        if (($severClass = $this->getServerClassName($type) ?? null) && empty($severClass)) {
            throw new InvalidArgumentException(sprintf('Unsupported server type: %s.', $type));
        }

        return new $severClass(
            array_merge($this->getDefaultConf($type), $configure)
        );
    }

    /**
     * 创建单一服务
     *
     * @param array $configure
     * @param $kernelClass
     *
     * @return ServerInterface
     */
    protected function createSingleServer(array $configure, ?string &$kernelClass = null)
    {
        /**
         * @var $kernelClass string
         */
        $kernelClass = Arr::pull($configure, 'kernel');

        return $this->createServer($configure);
    }

    /**
     * 创建混合型服务
     *
     * @param array $configure
     * @param string|null $kernelClass
     *
     * @return ServerInterface
     */
    protected function createMultipleMixServer(array $configure, ?string &$kernelClass = null)
    {
        $types = array_column($configure, 'type');
        $index = array_search(self::TYPE_WEBSOCKET, $types, true);
        $index = $index === false ? array_search(self::TYPE_HTTP, $types, true) : $index;
        $index = $index === false ? 0 : $index;

        $serverConfig = Arr::pull($configure, $index);
        $kernelClass = $serverConfig['kernel'] ?? '';
        $server = $this->createServer($serverConfig);

        foreach ($configure as $conf) {
            $server->addListen(
                array_merge($this->getDefaultConf($conf['type']), $conf),
                $this->getKernel($conf['kernel']),
                $conf['setting'] ?? null
            );
        }

        return $server;
    }

}