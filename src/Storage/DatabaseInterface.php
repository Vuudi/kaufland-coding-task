<?php

namespace App\Storage;

interface DatabaseInterface
{
    public function connect();

    public function insertData(array $data);
}
