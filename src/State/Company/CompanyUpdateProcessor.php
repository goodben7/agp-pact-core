<?php

namespace App\State\Company;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Company\CompanyUpdateDTO;
use App\Entity\Company;
use App\Manager\CompanyManager;
use App\Model\UpdateCompanyModel;

readonly class CompanyUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private CompanyManager $manager
    ) {
    }

    /**
     * @param CompanyUpdateDTO $data
     */
    public function process($data, Operation $operation, $uriVariables = [], $context = []): Company
    {
        $model = new UpdateCompanyModel(
            $data->name,
            $data->type,
            $data->contactEmail,
            $data->contactPhone,
            $data->active,
            $data->roadAxes,
            $data->canProcessSensitiveComplaint
        );

        return $this->manager->updateFrom($model, $uriVariables['id']);
    }
}