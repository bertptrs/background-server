<?php

namespace bertptrs\background_server;

use bertptrs\background_server\error\NotStartedException;
use bertptrs\background_server\error\TimeoutException;

class BackgroundServerImpl implements BackgroundServer
{
    private $process;
    private $checkers;

    /**
     * BackgroundServerImpl constructor.
     * @param resource $process A reference to a created process.
     * @param ReadinessCheck[] $checkers
     */
    public function __construct($process, array $checkers)
    {
        $this->process = $process;
        $this->checkers = $checkers;
    }

    /**
     * Test if this process is currently running
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        $details = proc_get_status($this->process);
        return $details['running'];
    }

    /**
     * Stop the current background server if it is running.
     *
     * @return bool true if it was originally running.
     */
    public function stop(): bool
    {
        if (!$this->isRunning()) {
            return false;
        }
        proc_terminate($this->process);
        return true;
    }

    /**
     * Wait until the process is ready.
     *
     * @param float $timeout Time unt
     * @throws NotStartedException
     * @throws TimeoutException
     * @return bool
     */
    public function awaitReady(float $timeout): bool
    {
        if (!$this->isRunning()) {
            throw new NotStartedException();
        }

        foreach ($this->checkers as $check) {
            if (!$check->awaitReady($timeout)) {
                throw new TimeoutException();
            }
        }

        return true;
    }
}
