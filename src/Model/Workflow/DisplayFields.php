<?php

namespace App\Model\Workflow;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class DisplayFields
{
    #[
        Assert\NotBlank,
        Assert\Length(max: 255)
    ]
    #[Groups(['workflow_step:get', 'workflow_step:list'])]
    public string $name;

    #[
        Assert\NotBlank,
        Assert\Length(max: 255)
    ]
    #[Groups(['workflow_step:get', 'workflow_step:list'])]
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
    #[Groups(['workflow_step:get', 'workflow_step:list'])]
    public string $type;
}
