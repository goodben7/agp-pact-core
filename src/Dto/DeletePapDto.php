<?php

namespace App\Dto;

use App\Entity\Pap;
use Symfony\Component\Validator\Constraints as Assert;

class DeletePapDto
{
    public function __construct(

        #[Assert\Choice(choices: [Pap::REASON_DELETION_ERRONEE, Pap::REASON_DELETION_ERREUR_CREATION])]
        public string $reason
    )
    {
    }
}