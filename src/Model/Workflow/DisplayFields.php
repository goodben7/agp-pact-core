<?php

namespace App\Model\Workflow;

use Symfony\Component\Validator\Constraints as Assert;

class DisplayFields
{
    #[
        Assert\NotBlank,
        Assert\Length(max: 255)
    ]
    public string $name;

    #[
        Assert\NotBlank,
        Assert\Length(max: 255)
    ]
    public string $label;

    #[
        Assert\NotBlank,
        Assert\Length(max: 50),
        Assert\Choice([
            'text',
            'number',
            'email',
            'date',
            'select',
            'checkbox',
            'radio',
            'textarea'
        ]),
    ]
    public string $type;
}
