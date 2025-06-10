<?php

namespace App\Service;

interface IdGeneratorInterface
{
    public function nextValueFor(string $prefix, array $options = []): int;
}
