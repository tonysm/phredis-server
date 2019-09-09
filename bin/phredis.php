<?php

require __DIR__.'/../vendor/autoload.php';

$port = $argv[1] ?? '6379';

use Amp\Loop;
use Tonysm\Phredis\State;
use function Amp\asyncCall;
use Tonysm\Phredis\Server as PhredisServer;

Loop::run(function () use ($port) {
    $uri = "tcp://127.0.0.1:{$port}";

    echo "Stared running Phredis Server at {$uri}\n";

    $state = new State();
    $server = Amp\Socket\Server::listen($uri);
    $clientHandler = new PhredisServer($state);

    while ($socket = yield $server->accept()) {
        asyncCall($clientHandler, $socket);
    }
});