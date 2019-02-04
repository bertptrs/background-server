<?php

namespace bertptrs\background_server\tests\readiness;

use bertptrs\background_server\readiness\PortCheck;
use PHPUnit\Framework\TestCase;

class PortCheckTest extends TestCase
{
    private $process;

    public function setUp()
    {
        $this->process = null;
    }

    public function tearDown()
    {
        if ($this->process !== null) {
            proc_terminate($this->process);
        }
    }

    public function testPortCheck()
    {
        $instance = new PortCheck('localhost', 6464);
        $this->assertFalse($instance->isReady());

        $this->process = proc_open('exec nc -l -p 6464', [STDIN, STDOUT, STDERR], $pipes);

        if ($this->process === false) {
            $this->markTestSkipped('Failed to start background process');
        }

        $this->assertTrue($instance->awaitReady(2));
    }
}
