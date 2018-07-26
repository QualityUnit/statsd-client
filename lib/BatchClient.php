<?php

namespace Qu\Statsd;

use Qu\Statsd\Connection\Connection;

class BatchClient extends AbstractClient {

    /** @var Connection */
    private $connection;
    /** @var string[] */
    private $batch = [];

    public function __construct(Connection $connection, string $namespace = '') {
        parent::__construct($namespace);
        $this->connection = $connection;
    }

    /**
     * Send all stats since last commit
     */
    public function commit() {
        $this->connection->sendMessages($this->batch);
        $this->batch = [];
    }

    protected function sendRawData(string $dataToSend) {
        $this->batch[] = $dataToSend;
    }
}