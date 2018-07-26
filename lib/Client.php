<?php

namespace Qu\Statsd;

use Qu\Statsd\Connection\Connection;

class Client extends AbstractClient {

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection, string $namespace = '') {
        parent::__construct($namespace);
        $this->connection = $connection;
    }

    public function batch() {
        return new BatchClient($this->connection, $this->namespace);
    }

    protected function sendRawData(string $dataToSend) {
        $this->connection->send($dataToSend);
    }
}