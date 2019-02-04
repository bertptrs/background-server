<?php

namespace bertptrs\background_server\readiness;

use bertptrs\background_server\ReadinessCheck;

class PortCheck implements ReadinessCheck
{
    private $host;
    private $port;

    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function isReady(float $timeout = 0): bool
    {
        try {
            $socket = @fsockopen($this->host, $this->port, $errno, $errStr, $timeout);
        } finally {
            if (!empty($socket)) {
                fclose($socket);
                return true;
            }

            return false;
        }
    }

    public function awaitReady(float $timeout): bool
    {
        $start = microtime(true);

        while (microtime(true) - $start < $timeout) {
            if ($this->isReady($timeout)) {
                return true;
            }
        }

        return false;
    }
}
