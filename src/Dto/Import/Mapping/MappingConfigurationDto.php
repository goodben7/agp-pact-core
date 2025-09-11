<?php

namespace App\Dto\Import\Mapping;

use Symfony\Component\Validator\Constraints as Assert;

readonly class MappingConfigurationDto
{
    /**
     * @param MappingColumnDto[] $columns
     */
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Valid]
        #[Assert\Count(min: 1, minMessage: "Vous devez définir au moins une colonne.")]
        public array              $columns = [],

        #[Assert\Valid]
        public ?MappingOptionsDto $options = null
    )
    {
    }
}