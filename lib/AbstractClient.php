<?php

namespace Qu\Statsd;

abstract class AbstractClient {

    /** @var string */
    protected $namespace;

    public function __construct(string $namespace = '') {
        $this->namespace = $namespace;
    }

    /**
     * Increment counter by value.
     *
     * @param string $key
     * @param int $value
     * @param float $sampleRate only send percentage of reported stats (0..1)
     * @param array $tags
     *
     * @return $this
     */
    public function count(string $key, int $value, float $sampleRate = 1, array $tags = []) {
        return $this->send($key, $value, 'c', $sampleRate, $tags);
    }

    /**
     * Decrement counter by 1.
     *
     * @param string $key
     * @param float $sampleRate only send percentage of reported stats (0..1)
     * @param array $tags
     *
     * @return $this
     */
    public function decrement(string $key, float $sampleRate = 1, array $tags = []) {
        return $this->count($key, -1, $sampleRate, $tags);
    }

    /**
     * Set gauge to value.
     *
     * @param string $key
     * @param int $value
     * @param array $tags
     *
     * @return $this
     */
    public function gauge(string $key, int $value, array $tags = []) {
        return $this->send($key, $value, 'g', 1, $tags);
    }

    /**
     * Increment counter by 1.
     *
     * @param string $key
     * @param float $sampleRate only send percentage of reported stats (0..1)
     * @param array $tags
     *
     * @return $this
     */
    public function increment(string $key, float $sampleRate = 1, array $tags = []) {
        return $this->count($key, 1, $sampleRate, $tags);
    }

    /**
     * Add value to the set.
     *
     * @param string $key
     * @param int $value
     * @param array $tags
     *
     * @return $this
     */
    public function set(string $key, int $value, array $tags = []) {
        return $this->send($key, $value, 's', 1, $tags);
    }

    /**
     * Add timing value.
     *
     * @param string $key
     * @param int $value in milliseconds
     * @param float $sampleRate only send percentage of reported stats (0..1)
     * @param array $tags
     *
     * @return AbstractClient
     */
    public function timing(string $key, int $value, float $sampleRate = 1, array $tags = []) {
        return $this->send($key, $value, 'ms', $sampleRate, $tags);
    }

    abstract protected function sendRawData(string $dataToSend);

    private function send(string $key, int $value, string $type, float $sampleRate, array $tags = []) {
        if (mt_rand() / mt_getrandmax() > $sampleRate) {
            return $this;
        }

        if ('' !== $this->namespace) {
            $key = $this->namespace . '.' . $key;
        }

        $message = $key . ':' . $value . '|' . $type;

        if ($sampleRate < 1) {
            $sampledData = $message . '|@' . $sampleRate;
        } else {
            $sampledData = $message;
        }

        if (!empty($tags)) {
            $sampledData .= '|#';
            $tagArray = [];
            foreach ($tags as $tagKey => $tagValue) {
                $tagArray[] = ($tagKey . ':' . $tagValue);
            }
            $sampledData .= implode(',', $tagArray);
        }

        $this->sendRawData($sampledData);

        return $this;
    }
}