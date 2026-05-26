<?php

namespace App\Libraries;

class PasswordGenerator
{
    private const UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const LOWER = 'abcdefghijklmnopqrstuvwxyz';
    private const DIGITS = '0123456789';
    private const SYMBOLS = '!@#$%^&*';

    public static function generate(): string
    {
        $pool = self::UPPER . self::LOWER . self::DIGITS . self::SYMBOLS . self::UPPER . self::LOWER . self::DIGITS . self::SYMBOLS;

        $password = '';
        $password .= self::UPPER[random_int(0, 25)];
        $password .= self::UPPER[random_int(0, 25)];
        $password .= self::LOWER[random_int(0, 25)];
        $password .= self::LOWER[random_int(0, 25)];
        $password .= self::DIGITS[random_int(0, 9)];
        $password .= self::DIGITS[random_int(0, 9)];
        $password .= self::SYMBOLS[random_int(0, 7)];
        $password .= self::SYMBOLS[random_int(0, 7)];

        for ($i = 0; $i < 4; $i++) {
            $password .= $pool[random_int(0, strlen($pool) - 1)];
        }

        return str_shuffle($password);
    }
}