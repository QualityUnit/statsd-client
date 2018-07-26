<?php

namespace Qu\Statsd\Connection;

abstract class Socket implements Connection {

    /** @var string */
    private $host;
    /** @var int */
    private $port;
    /** @var int|null */
    private $timeout;
    /**
     * Maximum Transmission Unit
     * http://en.wikipedia.org/wiki/Maximum_transmission_unit
     *
     * @var int
     */
    private $mtu;

    public function __construct(string $host = 'localhost', int $port = 8125, int $timeout = 3, int $mtu = 1500) {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->mtu = $mtu;
    }

    public function getHost(): string {
        return $this->host;
    }

    public function getPort(): int {
        return $this->port;
    }

    /**
     * @return int|null
     */
    public function getTimeout() {
        return $this->timeout;
    }

    /**
     * @inheritdoc
     */
    public function send(string $message) {
        if ($message === '') {
            return;
        }

        if (!$this->isConnected()) {
            $this->connect($this->host, $this->port, $this->timeout);
        }

        $this->writeToSocket($message);
    }

    /**
     * @inheritdoc
     */
    public function sendMessages(array $messages) {
        $message = implode("\n", $messages);

        if (\strlen($message) > $this->mtu) {
            $messageBatches = $this->cutIntoMtuSizedMessages($messages);

            foreach ($messageBatches as $messageBatch) {
                $this->send(implode("\n", $messageBatch));
            }
        } else {
            $this->send($message);
        }
    }

    /**
     * Initiates a connection.
     *
     * @param string $host
     * @param int $port
     * @param int|null $timeout
     */
    abstract protected function connect(string $host, int $port, int $timeout = null);

    /**
     * @return bool true if connection is open, false otherwise
     */
    abstract protected function isConnected(): bool;

    abstract protected function writeToSocket(string $message);

    private function cutIntoMtuSizedMessages(array $messages): array {
        $index = 0;
        $sizedMessages = [];
        $packageLength = 0;

        foreach ($messages as $message) {
            $messageLength = \strlen($message);

            if ($messageLength + $packageLength > $this->mtu) {
                ++$index;
                $packageLength = 0;
            }

            $sizedMessages[$index][] = $message;
            $packageLength += $messageLength;
        }

        return $sizedMessages;
    }
}
