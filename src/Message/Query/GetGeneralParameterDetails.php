<?php

namespace App\Message\Query;

class GetGeneralParameterDetails implements QueryInterface
{
    public function __construct(
        public ?string $id = null,
        public ?string $category = null,
    )
    {  
    }
}