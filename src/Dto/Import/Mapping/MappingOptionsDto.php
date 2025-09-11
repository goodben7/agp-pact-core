<?php

namespace App\Dto\Import\Mapping;

use Symfony\Component\Validator\Constraints as Assert;

readonly class MappingOptionsDto
{
    public function __construct(
        #[Assert\Length(min: 1, max: 1)]
        public ?string $delimiter = ',',

        public ?bool   $hasHeader = true
    )
    {
    }
}