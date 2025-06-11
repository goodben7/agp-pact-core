<?php

namespace App\Model\Workflow;

use Symfony\Component\Validator\Constraints as Assert;

class InputFields
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

    public bool $required;

    public ?string $optionsCategory = null;

    public ?string $defaultValue = null;

    public ?array $validationRules = null;
}
