<?php

namespace App\Message\Query;

class GetRoadAxisDetails implements QueryInterface
{
    public function __construct(
        public ?string $id = null
    )
    {  
    }
}