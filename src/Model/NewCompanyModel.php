<?php

namespace App\Model;

use App\Entity\GeneralParameter;

class NewCompanyModel
{
    public function __construct(
        public ?string           $name = null,
        public ?GeneralParameter $type = null,
        public ?string           $contactEmail = null,
        public ?string           $contactPhone = null,
        public ?bool             $active = true,
        public ?array            $locations = [],
        public ?array            $roadAxes = [],
        public ?bool             $canProcessSensitiveComplaint = null,
    )
    {
    }
}
