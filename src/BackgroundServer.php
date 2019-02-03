<?php

namespace bertptrs\BackgroundServer;

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
}
