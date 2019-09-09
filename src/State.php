<?php

namespace Tonysm\Phredis;

use Amp\Socket\Socket;

class State
{
    /** @var array */
    private $data = [];

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
                return $socket->write("+PONG\r\n");
                break;
            case "echo":
                $msg = array_slice($data, 4)[0] ?? "OK";
                return $socket->write("+$msg\r\n");
                break;
            case "set":
                $key = $data[4] ?? null;
                $value = $data[6] ?? null;
                $this->data[$key] = $value;
                return $socket->write("+OK\r\n");
                break;
            case "get":
                $key = $data[4] ?? null;
                $value = array_key_exists($key, $this->data) ? "{$this->data[$key]}" : null;

                if ($value === null) {
                    return $socket->write("\$-1\r\n");
                } else {
                    $length = strlen($value);
                    return $socket->write("\${$length}\r\n$value\r\n");
                }
                break;
            default:
                return $socket->write("+OK\r\n");
                break;
        }
    }
}