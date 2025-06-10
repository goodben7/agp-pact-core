<?php
namespace App\Dto;

use App\Entity\Profile;

use Symfony\Component\Validator\Constraints as Assert;


class SetUserProfileDto {

    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    public ?Profile $profile = null;
}