<?php

namespace App\Helpers;

class Hash
{
    public static function generateHash(string $text): string
    {
        return password_hash($text, PASSWORD_DEFAULT);
    }

    public static function verifyHash(string $text, string $hash): bool
    {
        return password_verify($text, $hash);
    }
}
