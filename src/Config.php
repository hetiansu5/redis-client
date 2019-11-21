<?php

namespace SimpleRedis;

/**
 * Class Config
 * @remark Redis配置
 *
 */
class Config
{

    const HOST = '127.0.0.1';
    const PORT = 6379;
    const TIMEOUT = 1;

    private $host;
    private $port;
    private $timeout; //单位秒
    private $password;
    private $db;

    /**
     * RedisClient constructor.
     * @param $config ['host' => '', 'port' => 6379, 'timeout' => 1]
     */
    public function __construct($config)
    {
        $this->parseConfig($config);
    }

    private function parseConfig($config)
    {
        if (isset($config['host'])) {
            $this->host = $config['host'];
        }
        if (isset($config['port']) && is_numeric($config['port'])) {
            $this->port = $config['port'];
        }
        if (isset($config['timeout']) && $config['timeout'] > 0) {
            $this->timeout = $config['timeout'];
        }
        if (isset($config['password'])) {
            $this->password = $config['password'];
        }
        if (isset($config['db'])) {
            $this->db = $config['db'];
        }
    }

    public function getHost()
    {
        return $this->host ?: self::HOST;
    }

    public function getPort()
    {
        return $this->port ?: self::PORT;
    }

    public function getTimeout()
    {
        return $this->timeout > 0 ? $this->timeout : self::TIMEOUT;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getDb()
    {
        return $this->db;
    }

}