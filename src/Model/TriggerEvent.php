<?php

namespace App\Model;

class TriggerEvent
{

    private ?string $description;

    public function __construct(
        private readonly string $triggerEventId,
        private readonly string $label
    )
    {
    }

    public static function new(string $triggerEventId, string $label): static
    {
        return new self($triggerEventId, $label);
    }

    /**
     * Get the value of description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param string|null $description
     * @return  self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of triggerEventId
     */
    public function getTriggerEventId(): string
    {
        return $this->triggerEventId;
    }

    /**
     * Get the value of label
     */
    public function getLabel(): string
    {
        return $this->label;
    }
}
