<?php

namespace App\Message\Query;

class GetLocationDetails implements QueryInterface
{
    public function __construct(
        public ?string $id = null
    )
    {  
    }
}