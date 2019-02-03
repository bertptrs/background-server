<?php

namespace bertptrs\BackgroundServer;

class BackgroundServerImpl implements BackgroundServer
{
    private $process;

    public function __construct($process)
    {
        $this->process = $process;
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
}
