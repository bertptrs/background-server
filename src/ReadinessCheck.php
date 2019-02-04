<?php

namespace bertptrs\background_server;

/**
 * Interface ReadyCheck
 *
 * Generalized check whether a background server is ready yet.
 */
interface ReadinessCheck
{
    /**
     * Check if the server is ready yet.
     *
     * @return bool true if it is.
     */
    public function isReady(): bool;

    /**
     * Await readiness of the server.
     *
     * Note: not every implementation can (or will) wait.
     *
     * @param float $timeout The maximum time to wait, in seconds.
     * @return bool true if the server is ready after the timeout, false otherwise.
     */
    public function awaitReady(float $timeout): bool;
}
