<?php

use Predis\Client;
use PHPUnit\Framework\TestCase;

class SkeletonTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        PhredisTestServer::start();
    }

    public function client()
    {
        return new Client([
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => $_SERVER['TEST_SERVER_PORT'],
        ]);
    }

    public function testRespondsToPing()
    {
        $this->assertEquals('PONG', $this->client()->ping());
    }

    public function testRespondsToEcho()
    {
        $this->assertEquals('LOREM IPSUM', $this->client()->echo('LOREM IPSUM'));
    }

    public function testSetsAndGets()
    {
        $redis = $this->client();
        $this->assertNull($redis->get('lorem'));
        $redis->set('lorem', '42');
        $this->assertEquals('42', $redis->get('lorem'));
    }
}

class PhredisTestServer
{
    public static function start()
    {
        $path = dirname(dirname(__DIR__)).'/bin/phredis.php';

        $proc = new Symfony\Component\Process\Process(
            "php {$path} {$_SERVER['TEST_SERVER_PORT']}"
        );
        $proc->start();

        static::waitForServer();

        register_shutdown_function(function () {
            @exec("kill $(ps aux | grep 'phredis.php' | awk '{print $2}')");
        });
    }

    private static function waitForServer()
    {
        do {
            $nc = new Symfony\Component\Process\Process(
                'nc -z localhost' . $_SERVER['TEST_SERVER_PORT'] . '|| echo broken'
            );
            $nc->run();
        } while (trim($nc->getOutput()) === "broken" && sleep(1));
    }
}