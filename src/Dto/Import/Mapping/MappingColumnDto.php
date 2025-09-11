<?php

namespace App\Dto\Import\Mapping;

use Symfony\Component\Validator\Constraints as Assert;

readonly class MappingColumnDto
{
    public function __construct(
        #[Assert\NotBlank]
        public ?string $fileHeader = null,

        #[Assert\NotBlank]
        public ?string $entityProperty = null,

        public ?bool $required = false,

        public ?string $format = null
    ) 
    {
    }
}