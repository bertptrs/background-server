<?php

namespace bertptrs\background_server;

use bertptrs\background_server\error\NotStartedException;
use bertptrs\background_server\readiness\PortCheck;

final class ServerBuilder
{
    private $executable = null;
    private $killOnExit = false;
    private $env = null;
    private $descriptors = [
        0 => STDIN,
        1 => STDOUT,
        2 => STDERR,
    ];
    private $checkers = [];

    public function __construct(string $executable)
    {
        $this->executable = $executable;
    }

    /**
     * Set the environment for the new process.
     *
     * @param array|null $env The new environment, or null to inherit the current environment.
     * @return ServerBuilder $this
     */
    public function setEnv(array $env = null): self
    {
        $this->env = $env;

        return $this;
    }

    /**
     * Mark the server to be killed on PHP exit.
     *
     * @param bool $kill whether to kill it, default true.
     * @return ServerBuilder $this
     */
    public function killOnExit($kill = true): self
    {
        $this->killOnExit = (bool)$kill;
        return $this;
    }

    public function start(): BackgroundServer
    {
        $process = proc_open('exec ' . $this->executable, $this->descriptors, $pipes, null, $this->env);
        if (!$process) {
            throw new NotStartedException('Failed to create process');
        }

        if ($this->killOnExit) {
            register_shutdown_function(function () use ($process) {
                proc_terminate($process);
            });
        }

        return new BackgroundServerImpl($process, $this->checkers);
    }

    /**
     * Add a general purpose readiness check to this build.
     *
     * @param ReadinessCheck $check The check to add
     * @return ServerBuilder this
     */
    public function addReadinessCheck(ReadinessCheck $check): self
    {
        $this->checkers[] = $check;
        return $this;
    }

    /**
     * Add a port check to the readiness checks.
     *
     * @param string $host
     * @param int $port
     * @return ServerBuilder
     */
    public function checkPort(string $host, int $port): self
    {
        $this->checkers[] = new PortCheck($host, $port);
        return $this;
    }

    /**
     * Utility method for easier chaining.
     *
     * @param string $executable The command line to start. Note that this command line will be exec'ed rather than start
     *
     * @return ServerBuilder
     */
    public static function create(string $executable): ServerBuilder
    {
        return new ServerBuilder($executable);
    }
}
