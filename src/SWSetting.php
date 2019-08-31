<?php declare(strict_types=1);


namespace Jeekens\Server;

/**
 * Class SWSetting
 *
 * @package Jeekens\Server3
 */
final class SWSetting
{

    private static $server = [
        'reactor_num',
        'worker_num',
        'max_request',
        'max_conn',
        'max_connection',
        'task_worker_num',
        'task_ipc_mode',
        'task_max_request',
        'task_tmpdir',
        'task_enable_coroutine',
        'task_use_object',
        'dispatch_mode',
        'dispatch_func',
        'message_queue_key',
        'daemonize',
        'backlog',
        'log_file',
        'log_level',
        'heartbeat_check_interval',
        'heartbeat_idle_time',
        'open_eof_check',
        'open_eof_split',
        'package_eof',
        'open_length_check',
        'package_length_type',
        'package_length_func',
        'package_max_length',
        'open_cpu_affinity',
        'cpu_affinity_ignore',
        'open_tcp_nodelay',
        'tcp_defer_accept',
        'ssl_cert_file',
        'ssl_method',
        'ssl_ciphers',
        'user',
        'group',
        'chroot',
        'pid_file',
        'pipe_buffer_size',
        'buffer_output_size',
        'socket_buffer_size',
        'enable_unsafe_event',
        'discard_timeout_request',
        'enable_reuse_port',
        'enable_delay_receive',
        'open_http_protocol',
        'open_http2_protocol',
        'open_websocket_protocol',
        'open_mqtt_protocol',
        'open_websocket_close_frame',
        'reload_async',
        'tcp_fastopen',
        'request_slowlog_file',
        'enable_coroutine',
        'max_coroutine',
        'ssl_verify_peer',
        'max_wait_time',
    ];

    /**
     * 获取swoole的所有可用设置项
     *
     * @return array
     */
    public static function getSetting(): array
    {
        return self::$server;
    }

}