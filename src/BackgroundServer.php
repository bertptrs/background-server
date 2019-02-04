<?php

namespace bertptrs\background_server;

use bertptrs\background_server\error\NotStartedException;
use bertptrs\background_server\error\TimeoutException;

interface BackgroundServer
{
    /**
     * Test if this process is currently running
     *
     * @return bool
     */
    public function isRunning(): bool;

    /**
     * Stop the current background server if it is running.
     *
     * @return bool true if it was originally running.
     */
    public function stop(): bool;

    /**
     * Wait until the process is ready.
     *
     * @param float $timeout Time unt
     * @throws NotStartedException
     * @throws TimeoutException
     * @return bool
     */
    public function awaitReady(float $timeout): bool;
}
