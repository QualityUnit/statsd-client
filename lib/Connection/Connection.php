<?php

namespace Qu\Statsd\Connection;

interface Connection {

    /**
     * Sends a message to StatsD.
     *
     * @param string $message
     */
    public function send(string $message);

    /**
     * Sends multiple messages to StatsD.
     *
     * @param array $messages
     */
    public function sendMessages(array $messages);
}