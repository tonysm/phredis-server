<?php

require __DIR__.'/../vendor/autoload.php';

$port = $argv[1] ?? '6379';

use Amp\Loop;
use Tonysm\Phredis\State;
use function Amp\asyncCall;
use Tonysm\Phredis\Server as PhredisServer;

$state = new State();
$clientHandler = new PhredisServer($state);

Loop::run(function () use ($port, $clientHandler) {
    $uri = "tcp://127.0.0.1:{$port}";

    echo "Stared running Phredis Server at {$uri}\n";

    $server = Amp\Socket\Server::listen($uri);

    while ($socket = yield $server->accept()) {
        asyncCall($clientHandler, $socket);
    }
});