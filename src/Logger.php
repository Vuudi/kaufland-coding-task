<?php

namespace App;

class Logger
{
    public static function log($message): void
    {
        file_put_contents('app.log', $message . PHP_EOL, FILE_APPEND);
    }
}
