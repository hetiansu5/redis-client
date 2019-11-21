<?php

namespace SimpleRedis;

/**
 * Class Socket
 * @remark Socket网络IO相关
 *
 */
class Socket
{

    /**
     * @var Config
     */
    private $config;

    private $socket;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    private function _instance()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ["sec" => $this->config->getTimeout(), "usec" => 0]);
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, ["sec" => $this->config->getTimeout(), "usec" => 0]);
    }

    public function setOption($level, $optName, $optVal)
    {
        if (!$this->socket) {
            $this->_instance();
        }
        socket_set_option($this->socket, $level, $optName, $optVal);
    }

    public function connect()
    {
        if (!$this->socket) {
            $this->_instance();
        }
        return socket_connect($this->socket, $this->config->getHost(), $this->config->getPort());
    }

    /**
     * @param $message
     * @return bool|int
     */
    public function write($message)
    {
        if (!$this->socket) {
            $this->connect();
        }

        return socket_write($this->socket, $message, strlen($message));
    }

    /**
     * @param $length
     * @param $type
     * @return bool|string
     */
    public function read($length, $type = PHP_BINARY_READ)
    {
        if (!$this->socket) {
            $this->connect();
        }

        return socket_read($this->socket, $length, $type);
    }

    /**
     * @param $length
     * @return bool|string
     */
    public function readAll($length)
    {
        $message = $slice = $this->read($length);

        while (strlen($slice) >= $length) {
            $slice = $this->read($length);
            $message .= $slice;
        }

        return $message;
    }

    /**
     * @return int
     */
    public function getLastErrNo()
    {
        return socket_last_error($this->socket);
    }

    public function getLastError()
    {
        return socket_strerror($this->getLastErrNo());
    }

    public function close()
    {
        if ($this->socket) {
            socket_close($this->socket);
            $this->socket = null;
        }
    }

    public function __destruct()
    {
        $this->close();
    }

}