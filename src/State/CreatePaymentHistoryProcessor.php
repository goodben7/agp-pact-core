<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Manager\ParManager;
use App\Model\NewPaymentHistoryModel;

class CreatePaymentHistoryProcessor implements ProcessorInterface
{
    public function __construct(private ParManager $manager)
    {
        
    }

    /**
     * @param \App\Dto\CreatePaymentHistoryDto $data 
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $model = new NewPaymentHistoryModel(
            $data->parId, 
            $data->paymentDate, 
            $data->amount, 
            $data->transactionReference, 
            $data->paymentMethod,
            $data->notes
        );

        return $this->manager->recordPayment($model);
    }
}
