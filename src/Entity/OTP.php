<?php

namespace App\Entity;

use App\Repository\OTPRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OTPRepository::class)]
class OTP
{
    const TYPE_REGISTRATION = "registration";      // Registration verification
    const TYPE_LOGIN = "login";             // Login verification
    const TYPE_PASSWORD_RESET = "password_reset";    // Password reset
    const TYPE_TRANSACTION = "transaction";       // Transaction authorization
    const TYPE_PROFILE_UPDATE = "profile_update";    // Profile information update
    const TYPE_EMAIL_CHANGE = "email_change";      // Email change verification
    const TYPE_PHONE_CHANGE = "phone_change";      // Phone number change verification
    const TYPE_WITHDRAWAL = "withdrawal";        // Withdrawal authorization

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expiryDate = null;

    #[ORM\Column(length: 6)]
    private ?string $code = null;

    #[ORM\Column]
    private ?bool $send = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getExpiryDate(): ?\DateTimeImmutable
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(\DateTimeImmutable $expiryDate): static
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function isSend(): ?bool
    {
        return $this->send;
    }

    public function setSend(bool $send): static
    {
        $this->send = $send;

        return $this;
    }
}
