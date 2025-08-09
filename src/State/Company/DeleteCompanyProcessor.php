<?php

namespace App\State\Company;

use ApiPlatform\State\ProcessorInterface;
use App\Manager\CompanyManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

readonly class DeleteCompanyProcessor implements ProcessorInterface
{
    public function __construct(private CompanyManager $manager)
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function process(mixed $data, \ApiPlatform\Metadata\Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $this->manager->delete($uriVariables['id']);
    }
}
