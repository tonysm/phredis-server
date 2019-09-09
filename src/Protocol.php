<?php

namespace Tonysm\Phredis;

class Protocol
{
    /**
     * @param $value
     * @return string
     *
     * @throws \Exception
     */
    public static function marshal($value): string
    {
        switch (true) {
            case $value === Symbol::PONG():
                return "+PONG\r\n";
            case $value === Symbol::OK():
                return "+OK\r\n";
            case is_null($value):
                return "\$-1\r\n";
            case is_array($value):
                $length = count($value);
                $strings = implode('', array_map(function ($val) {
                    return static::marshal($val);
                }, $value));
                return "*{$length}\r\n{$strings}";
            case is_string($value):
                $length = strlen($value);
                return "\${$length}\r\n$value\r\n";
            default:
                throw new \Exception('Unknown response type');
        }
    }
}