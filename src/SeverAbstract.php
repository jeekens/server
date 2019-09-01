<?php declare(strict_types=1);


namespace Jeekens\Server;


use RuntimeException;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server;
use Throwable;
use function array_map;
use function array_merge;
use function object_init;
use function sprintf;
use function var_dump;

/**
 * Class Sever
 *
 * @package Jeekens\Server
 */
abstract class SeverAbstract implements ServerInterface
{

    /**
     * @var string
     */
    protected $host = null;

    /**
     * @var int
     */
    protected $port = null;

    /**
     * @var int
     */
    protected $sockType = null;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var bool
     */
    protected $isBoot = false;

    /**
     * @var string
     */
    protected $pidFile = null;

    /**
     * @var string
     */
    protected $logFile = null;

    /**
     * @var int
     */
    protected $mode = null;

    /**
     * @var
     */
    protected $setting = null;

    /**
     * @var bool
     */
    protected $daemon = false;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var array
     */
    protected $listen;

    /**
     * @var Server\Port[]
     */
    protected $listeners;

    /**
     * @return bool
     */
    public function isBoot(): bool
    {
        return $this->isBoot;
    }

    /**
     * Sever constructor.
     *
     * @param array $configure
     */
    public function __construct(array $configure)
    {

        if (isset($configure['setting'])) {
            $this->setSetting($configure['setting']);
        }

        object_init($this, $this->serverArgsFormat($configure));
    }

    /**
     * @return static
     */
    public function start()
    {
        if (!$this->isBoot() && ($this->isBoot = true)) {

            $serverClass = $this->getServerClass();

            try {

                if ($this->getPidFile() === null ||
                    $this->getLogFile() === null ||
                    $this->getHost() === null ||
                    $this->getPort() === null ||
                    $this->getSockType() === null ||
                    $this->getMode() === null
                ) {
                    throw new RuntimeException('Host, port, mode, sock_type, pid file and log file must be set.');
                }

                $server = new $serverClass(
                    $this->getHost(),
                    $this->getPort(),
                    $this->getMode(),
                    $this->getSockType()
                );

                if (!($server instanceof Server)) {
                    throw new RuntimeException(sprintf('Server must be a "%s" instance.', Server::class));
                }

                $server->set(array_merge([
                    'log_file' => $this->getLogFile(),
                    'pid_file' => $this->getPidFile(),
                    'daemonize' => (int)$this->isDaemon(),
                ], $this->getSetting() ?? []));

                $this->registerEventHandle($server, $this->kernel);

                if (!empty($this->listen)) {
                    array_map(function ($portListener) use ($server) {
                        /**
                         * @var $port Server\Port
                         */
                        $port = $server->addlistener(
                            $portListener['host'],
                            $portListener['port'],
                            $portListener['sock_type']
                        );

                        if ($portListener['setting'] !== null) {
                            $port->set($portListener['setting']);
                        }

                        $this->registerEventHandle($port, $portListener['kernel']);
                        $this->listeners[] = $port;
                    }, $this->listen);
                }

                $server->start();
                $this->server = $server;

            } catch (Throwable $e) {
                echo $e->getMessage();
                die(0);
            }
        }

        return $this;
    }

    /**
     * @param array $setting
     *
     * @return static
     */
    public function setSetting(array $setting)
    {
        $this->setting = $setting;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getSetting(): ?array
    {
        return $this->setting;
    }

    /**
     * @param array $config
     * @param KernelInterface $kernel
     * @param array|null $setting
     *
     * @return $this
     */
    public function addListen(array $config, KernelInterface $kernel, ?array $setting = null)
    {
        $portListen = $this->portArgsFormat($config);
        $portListen['setting'] = $setting;
        $portListen['kernel'] = $kernel;
        $this->listen[] = $portListen;

        return $this;
    }

    /**
     * @param KernelInterface $kernel
     *
     * @return static
     */
    public function registerKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        return $this;
    }

    /**
     * @return string
     */
    public function getPidFile(): string
    {
        return $this->pidFile;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param Server|Server\Port $server
     * @param KernelInterface $kernel
     */
    protected function registerEventHandle($server, KernelInterface $kernel)
    {
        $eventHandlerIterator = $kernel->getEventHandlerIterator();

        if (! empty($eventHandlerIterator)) {
            $eventHandlerIterator->rewind();

            foreach ($eventHandlerIterator as $eventHandler) {
                $server->on($eventHandler->getEventName(), $eventHandler->getHandler());
            }
        }
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function portArgsFormat(array $config)
    {
        return [
            'host' => $config['host'] ?? $this->host,
            'port' => $config['port'] ?? $this->port,
            'sock_type' => $config['sock_type'] ?? $this->sockType,
        ];
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function serverArgsFormat(array $config)
    {
        return [
            'host' => $config['host'] ?? $this->host,
            'port' => $config['port'] ?? $this->port,
            'mode' => $config['mode'] ?? $this->mode,
            'sock_type' => $config['sock_type'] ?? $this->sockType,
        ];
    }

    /**
     * @return bool
     */
    public function isDaemon(): bool
    {
        return $this->daemon;
    }

    /**
     * @param bool $daemon
     *
     * @return static
     */
    public function daemon(bool $daemon)
    {
        $this->daemon = $daemon;

        return $this;
    }

    /**
     * @param string $logfile
     *
     * @return static
     */
    public function setLogFile(string $logfile)
    {
        $this->logFile = $logfile;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLogFile(): ?string
    {
        return $this->logFile;
    }

    /**
     * @param int $port
     *
     * @return static
     */
    public function setPort(int $port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @param int $mode
     *
     * @return static
     */
    public function setMode(int $mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @param string $host
     *
     * @return static
     */
    public function setHost(string $host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param int $sockType
     *
     * @return static
     */
    public function setSockType(int $sockType)
    {
        $this->sockType = $sockType;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSockType(): ?int
    {
        return $this->sockType;
    }

    /**
     * @return int|null
     */
    public function getMode(): ?int
    {
        return $this->mode;
    }

    /**
     * @param string $pidFile
     *
     * @return static
     */
    public function setPidFile(string $pidFile)
    {
        $this->pidFile = $pidFile;

        return $this;
    }

    public function reload()
    {
        // TODO: Implement reload() method.
    }

    public function stop()
    {
        // TODO: Implement stop() method.
    }

    public function restart()
    {
        // TODO: Implement restart() method.
    }

    /**
     * @return string
     */
    abstract protected function getServerClass(): string;

}