<?php

namespace App\Model;



class NewRegisterUserModel
{
    public function __construct(
        
        public ?string $email = null,
        
        public ?string $plainPassword = null, 

        public ?string $phone = null,

        public ?string $displayName = null,

    )
    {  
    }
}