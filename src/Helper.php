<?php declare(strict_types=1);

if (!function_exists('get_local_ip')) {
    /**
     * 获取本机所有网络接口的ip地址
     *
     * @return array
     */
    function get_local_ip(): array
    {
        return swoole_get_local_ip();
    }
}

if(!function_exists('support_sw_version')) {
    /**
     * 返回最低要求的swoole版本
     *
     * @return string
     */
    function support_sw_version() {
        return '4.4.5';
    }
}