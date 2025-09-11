<?php

namespace App\Dto\Import;

use App\Dto\Import\Mapping\MappingConfigurationDto;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateImportMappingDto
{
    public function __construct(
        #[Assert\NotBlank]
        public ?string                  $name = null,

        #[Assert\NotBlank]
        public ?string                  $entityType = null,

        #[Assert\Valid]
        public ?MappingConfigurationDto $mappingConfiguration = null
    )
    {
    }
}