<?php

namespace App\Model;

use App\Entity\PaymentHistory;
use Symfony\Component\Validator\Constraints as Assert;

class NewPaymentHistoryModel
{
    public function __construct(
  
        #[Assert\NotBlank(message: "L'identifiant PAR est requis.")]
        #[Assert\NotNull(message: "L'identifiant PAR ne peut pas être nul.")]
        public?string $parId = null,
        
        #[Assert\NotBlank(message: "La date de paiement est requise.")]
        #[Assert\NotNull(message: "La date de paiement ne peut pas être nulle.")]
        public?\DateTimeImmutable $paymentDate = null,
        
        #[Assert\NotBlank(message: "Le montant est requis.")]
        #[Assert\NotNull(message: "Le montant ne peut pas être nul.")]
        #[Assert\Regex(pattern: "/^\d+(\.\d{1,2})?$/", message: "Le montant doit être un nombre valide.")]
        public?string $amount = null,
        
        public?string $transactionReference = null,
        
        #[Assert\Choice(
            choices: [
                PaymentHistory::PAYMENT_METHOD_CASH,
                PaymentHistory::PAYMENT_METHOD_BANK_TRANSFER,
                PaymentHistory::PAYMENT_METHOD_MOBILE_MONEY,
                PaymentHistory::PAYMENT_METHOD_CHECK,
                PaymentHistory::PAYMENT_METHOD_CREDIT_CARD,
                PaymentHistory::PAYMENT_METHOD_OTHER
            ],
            message: "La méthode de paiement doit être l'une des valeurs suivantes : cash, bank_transfer, mobile_money, check, credit_card, other."
        )]
        public?string $paymentMethod = null,
        
        public?string $notes = null,    
        
    )
    {
    }
}