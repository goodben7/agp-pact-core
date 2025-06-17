<?php

namespace App\MessageHandler;

use App\Exception\UnavailableDataException;
use App\Message\Command\GenerateReportCommand;
use App\Repository\ReportTemplateRepository;
use App\Repository\UserRepository;
use App\Entity\GeneratedReport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;
use App\Service\Generator\PdfGeneratorService;
use App\Service\Generator\ExcelGeneratorService;
use App\Service\Generator\CsvGeneratorService;
use App\Service\ReportDataCollectorService;

#[AsMessageHandler]
class GenerateReportHandler
{
    public function __construct(
        private readonly EntityManagerInterface     $entityManager,
        private readonly ReportTemplateRepository   $reportTemplateRepository,
        private readonly UserRepository             $userRepository,
        private readonly LoggerInterface            $logger,
        private readonly PdfGeneratorService        $pdfGeneratorService,
        private readonly ExcelGeneratorService      $excelGeneratorService,
        private readonly CsvGeneratorService        $csvGeneratorService,
        private readonly ReportDataCollectorService $reportDataCollectorService,
        private string                              $projectDir
    )
    {
    }

    public function __invoke(GenerateReportCommand $command): ?GeneratedReport
    {
        $reportTemplateId = $command->getReportTemplateId();
        $filters = $command->getFilters();
        $requestedByUserId = $command->getRequestedByUserId();

        $reportTemplate = $this->reportTemplateRepository->find($reportTemplateId);
        $user = $this->userRepository->find($requestedByUserId);

        if (!$reportTemplate || !$user) {
            throw new UnavailableDataException(sprintf('Cannot generate report: Template %s or user %s not found.', $reportTemplateId, $requestedByUserId));
        }

        $generatedReport = (new GeneratedReport())
            ->setTemplate($reportTemplate)
            ->setRequestedBy($user)
            ->setFiltersApplied($filters)
            ->setStatus('generating');

        $this->entityManager->persist($generatedReport);
        $this->entityManager->flush();

        try {
            $reportTypeName = $reportTemplate->getReportType()->getCode();
            $reportFormatName = $reportTemplate->getFormat()->getCode();
            $templatePathOrContent = $reportTemplate->getTemplatePathOrContent();

            if (!$templatePathOrContent)
                throw new \RuntimeException(sprintf('Report template "%s" has no template path or content defined.', $reportTemplate->getName()));

            $reportData = $this->reportDataCollectorService->collectData($reportTypeName, $filters);
            $reportData['generatedAt'] = new \DateTimeImmutable();
            $reportData['generatedBy'] = $user->getDisplayName();


            $outputFileNameBase = $command->getOutputFileName() ?? str_replace(' ', '_', $reportTemplate->getName()) . '_' . (new \DateTimeImmutable())->format('YmdHis');
            $outputFilename = '';
            $filePath = '';

            switch ($reportFormatName) {
                case 'PDF':
                    $outputFilename = $outputFileNameBase . '.pdf';
                    $filePath = $this->pdfGeneratorService->generateFromHtmlTemplate($templatePathOrContent, ['reportData' => $reportData], $outputFilename);
                    break;
                case 'XLSX':
                    $outputFilename = $outputFileNameBase . '.xlsx';
                    $filePath = $this->excelGeneratorService->generateFromData([
                        'headers' => $this->getExcelCsvHeaders($reportTypeName), // Helper pour les en-têtes
                        'results' => $reportData['results']
                    ], $outputFilename);
                    break;
                case 'CSV':
                    $outputFilename = $outputFileNameBase . '.csv';
                    $filePath = $this->csvGeneratorService->generateFromTemplate($templatePathOrContent, ['reportData' => $reportData], $outputFilename);
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Unsupported report format: %s', $reportFormatName));
            }

            $generatedReport->setStatus('completed');
            $generatedReport->setFilePath($filePath);
            $generatedReport->setFileName($outputFilename);
            $generatedReport->setCompletedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $this->logger->info(sprintf('Report "%s" (ID: %s) generated successfully: %s', $reportTemplate->getName(), $generatedReport->getId(), $filePath));

        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Failed to generate report "%s" (ID: %s): %s', $reportTemplate->getName(), $generatedReport->getId(), $e->getMessage()), ['exception' => $e]);

            $generatedReport->setStatus('failed');
            $generatedReport->setErrorMessage($e->getMessage());
            $this->entityManager->flush();
        }
        return $generatedReport;
    }

    private function getExcelCsvHeaders(string $reportTypeName): array
    {
        switch ($reportTypeName) {
            case 'COMPLAINT_SUMMARY':
                return ["ID Plainte", "Date Décl.", "Plaignant", "Type", "Statut", "Cause Incident", "Localisation", "Description"];
            case 'PAP_IMPACT_REPORT':
                return ["ID PAP", "Nom PAP", "Type Personne", "Téléphone Contact", "Email Contact", "Degré Vulnérabilité", "Localisation Affectée", "ID Plainte Liée", "Type Propriété", "Surface Affectée (m²)", "Activité Commerciale", "Nombre Jours Affectés", "Perte Revenu Locatif", "Assistance Déménagement", "Assistance Vulnérable", "Total Dollar", "Accord Lib. Lieu"];
            case 'WORKFLOW_PERFORMANCE':
                return ["ID Plainte", "Ancienne Étape", "Nouvelle Étape", "Action", "Date Action", "Acteur", "Durée Étape (jours)", "Commentaires"];
            default:
                return ["ID", "Nom", "Valeur"];
        }
    }
}
