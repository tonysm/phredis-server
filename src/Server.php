<?php

namespace Tonysm\Phredis;

use Amp\Socket\Socket;

class Server
{
    /**
     * @var \Tonysm\Phredis\State
     */
    private $state;

    public function __construct(State $state)
    {
        $this->state = $state;
    }

    /**
     * @param \Amp\Socket\Socket $socket
     * @return \Generator
     * @throws \Amp\ByteStream\ClosedException
     * @throws \Amp\ByteStream\StreamException
     */
    public function __invoke(Socket $socket)
    {
        while (null !== $chunk = yield $socket->read()) {
            $data = array_map('trim', explode("\n", trim($chunk)));

            yield $this->state->handle($data, $socket);
        }
    }
}