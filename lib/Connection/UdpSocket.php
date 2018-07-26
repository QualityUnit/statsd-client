<?php

namespace Qu\Statsd\Connection;

class UdpSocket extends Socket {

    /** @var resource|null|false */
    private $socket;
    /** @var bool */
    private $isConnected = false;

    /**
     * @inheritdoc
     */
    public function send(string $message) {
        try {
            parent::send($message);
        } catch (\Exception $ignore) {
        }
    }

    /**
     * @inheritdoc
     */
    protected function connect(string $host, int $port, int $timeout = null) {
        $this->socket = fsockopen("udp://$host", $port, $errNumber, $errMessage, $timeout);

        $this->isConnected = true;
    }

    /**
     * @inheritdoc
     */
    protected function isConnected(): bool {
        return $this->isConnected;
    }

    protected function writeToSocket(string $message) {
        @fwrite($this->socket, $message);
    }
}
