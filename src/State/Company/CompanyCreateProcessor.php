<?php

namespace App\State\Company;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Company\CompanyCreateDTO;
use App\Entity\Company;
use App\Manager\CompanyManager;
use App\Model\NewCompanyModel;
use Doctrine\ORM\EntityManagerInterface;

readonly class CompanyCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private CompanyManager $manager,
    )
    {
    }

    /**
     * @param CompanyCreateDTO $data
     */
    public function process($data, Operation $operation, $uriVariables = [], $context = []): Company
    {

        $model = new NewCompanyModel(
            $data->name,
            $data->type,
            $data->contactEmail,
            $data->contactPhone,
            $data->active,
            $data->roadAxes,
            $data->canProcessSensitiveComplaint
        );

        return $this->manager->create($model);
    }
}
