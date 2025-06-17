<?php

namespace App\State\Report;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Report\RequestReportDto;
use App\Entity\GeneratedReport;
use App\Entity\User;
use App\Message\Command\CommandBusInterface;
use App\Message\Command\GenerateReportCommand;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

readonly class RequestReportProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $bus,
        private Security            $security
    )
    {
    }

    /**
     * @param RequestReportDto $data
     */
    public function process($data, Operation $operation, $uriVariables = [], $context = []): GeneratedReport
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $report = $this->bus->dispatch(new GenerateReportCommand(
            reportTemplateId: $data->templateId,
            filters: $data->filters,
            requestedByUserId: $user->getId(),
            outputFileName: $data->outputFileName
        ));

        return $report;
    }
}
