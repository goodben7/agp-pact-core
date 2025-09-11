<?php

namespace App\MessageHandler\Query;

use App\Entity\GeneralParameter;
use App\Repository\GeneralParameterRepository;
use App\Message\Query\GetGeneralParameterDetails;
use App\Message\Query\QueryHandlerInterface;

class GetGeneralParameterDetailsHandler implements QueryHandlerInterface
{
    public function __construct(private GeneralParameterRepository $generalParameterRepository)
    {
    }

    public function __invoke(GetGeneralParameterDetails $query): ?GeneralParameter
    {
        if ($query->id !== null && $query->category !== null) {
            
            /** @var GeneralParameter|null $generalParameter */
            $generalParameter = $this->generalParameterRepository->findOneBy([
                'id' => $query->id,
                'category' => $query->category
            ]);
            
        } else {
            return null;
        }

        return $generalParameter;
    }
}
