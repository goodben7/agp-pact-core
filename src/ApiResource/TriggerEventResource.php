<?php

namespace App\ApiResource;

use App\Model\TriggerEvent;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Provider\TriggerEventProvider;

#[ApiResource(
    shortName: "TriggerEvent",
    operations: [
        new GetCollection(
            provider: TriggerEventProvider::class,
        )
    ]
)]
class TriggerEventResource
{

    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $value,
        public string $label,
    )
    {

    }

    public static function fromModel(TriggerEvent $p): static
    {
        return new self($p->getTriggerEventId(), $p->getLabel());
    }
}
