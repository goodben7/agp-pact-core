<?php

namespace App\Manager;

use App\Entity\Company;
use App\Entity\RoadAxis;
use App\Model\NewCompanyModel;
use App\Model\UpdateCompanyModel;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;
use App\Exception\UnauthorizedActionException;

readonly class CompanyManager
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
    }

    /**
     * Creates a new Company entity from the provided model.
     * @param NewCompanyModel $model
     * @return Company The newly created Company entity.
     */
    public function create(NewCompanyModel $model): Company
    {
        $company = (new Company())
            ->setName($model->name)
            ->setType($model->type)
            ->setContactEmail($model->contactEmail)
            ->setContactPhone($model->contactPhone)
            ->setActive($model->active)
            ->setCanProcessSensitiveComplaint($model->canProcessSensitiveComplaint);

        if (!empty($model->roadAxes)) {
            foreach ($model->roadAxes as $roadAxisId) {
                $roadAxis = $this->em->getRepository(RoadAxis::class)->find($roadAxisId);
                if ($roadAxis) {
                    $company->addRoadAxe($roadAxis);
                }
            }
        }

        $this->em->persist($company);
        $this->em->flush();

        return $company;
    }

    /**
     * Updates an existing Company entity from the provided model.
     * @param UpdateCompanyModel $model
     * @param string $companyId
     * @return Company The updated Company entity.
     * @throws UnavailableDataException
     */
    public function updateFrom(UpdateCompanyModel $model, string $companyId): Company
    {
        $company = $this->em->getRepository(Company::class)->find($companyId);
        
        if (!$company) {
            throw new UnavailableDataException("Company not found");
        }
        
        $company
            ->setName($model->name)
            ->setType($model->type)
            ->setContactEmail($model->contactEmail)
            ->setContactPhone($model->contactPhone)
            ->setActive($model->active)
            ->setCanProcessSensitiveComplaint($model->canProcessSensitiveComplaint);

        foreach ($company->getRoadAxes() as $roadAxis) {
            $company->removeRoadAxe($roadAxis);
        }
        
        if (!empty($model->roadAxes)) {
            foreach ($model->roadAxes as $roadAxisId) {
                $roadAxis = $this->em->getRepository(RoadAxis::class)->find($roadAxisId);
                if ($roadAxis) {
                    $company->addRoadAxe($roadAxis);
                }
            }
        }

        $this->em->flush();

        return $company;
    }

    public function delete(string $companyId): void 
    {
        $company = $this->findCompany($companyId);

        if ($company->getDeleted()) {
            throw new UnauthorizedActionException('this action is not allowed');
        }

        $company->setDeleted(true);

        $this->em->persist($company);
        $this->em->flush();
    }

    private function findCompany(string $companyId): Company 
    {
        $company = $this->em->find(Company::class, $companyId);

        if (null === $company) {
            throw new UnavailableDataException(sprintf('cannot find company with id: %s', $companyId));
        }

        return $company; 
    }
}