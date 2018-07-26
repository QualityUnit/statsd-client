<?php

namespace Qu\Statsd\Connection;

class MultiConnection implements Connection {

    /** @var Connection[] */
    private $connections = [];

    public function addConnection(Connection $connection) {
        $this->connections[] = $connection;
    }

    /**
     * @inheritdoc
     */
    public function send(string $message) {
        foreach ($this->connections as $connection) {
            $connection->send($message);
        }
    }

    /**
     * @inheritdoc
     */
    public function sendMessages(array $messages) {
        foreach ($this->connections as $connection) {
            $connection->sendMessages($messages);
        }
    }
}