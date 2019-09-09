<?php

namespace Tonysm\Phredis;

use Amp\Socket\Socket;

class State
{
    /** @var array */
    private $data = [];

    /** @var \Tonysm\Phredis\Protocol */
    private $protocol;

    public function __construct(Protocol $protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * @param array $data
     * @param \Amp\Socket\Socket $socket
     *
     * @return \Amp\Promise
     * @throws \Amp\ByteStream\ClosedException
     * @throws \Amp\ByteStream\StreamException
     */
    public function handle(array $data, Socket $socket)
    {
        $cmd = isset($data[2]) ? strtolower($data[2]) : null;

        switch ($cmd) {
            case "ping":
                return $this->sendResponse(Symbol::PONG(), $socket);
                break;
            case "echo":
                $msg = array_slice($data, 4)[0] ?? null;
                return $this->sendResponse($msg, $socket);
                break;
            case "set":
                $key = $data[4] ?? null;
                $value = $data[6] ?? null;
                $this->data[$key] = $value;
                return $this->sendResponse(Symbol::OK(), $socket);
                break;
            case "get":
                $key = $data[4] ?? null;
                $value = array_key_exists($key, $this->data) ? "{$this->data[$key]}" : null;

                return $this->sendResponse($value, $socket);
                break;
            case "keys":
                // we are only getting all keys here.
                // $pattern = $data[4] ?? null;
                return $this->sendResponse(array_reverse(array_keys($this->data)), $socket);
            default:
                return $this->sendResponse(Symbol::OK(), $socket);
                break;
        }
    }

    /**
     * @param $value
     * @param \Amp\Socket\Socket $socket
     * @return \Amp\Promise
     *
     * @throws \Amp\ByteStream\ClosedException
     * @throws \Amp\ByteStream\StreamException
     */
    private function sendResponse($value, Socket $socket)
    {
        return $socket->write($this->protocol->marshal($value));
    }
}