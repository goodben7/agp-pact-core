<?php

namespace App\Service\Generator;

use Twig\Environment;

readonly class CsvGeneratorService
{
    public function __construct(
        private Environment $twig,
        private string      $projectDir
    )
    {
    }

    /**
     * Génère un fichier CSV à partir d'un template Twig (qui doit rendre du CSV pur) et de données.
     *
     * @param string $templatePath Le chemin du template Twig (ex: 'reports/complaint_summary_report.csv.twig').
     * @param array $data Les données à passer au template Twig.
     * @param string $outputFilename Le nom du fichier de sortie (sans chemin).
     * @return string Le chemin complet du fichier CSV généré.
     * @throws \Exception Si la génération échoue.
     */
    public function generateFromTemplate(string $templatePath, array $data, string $outputFilename): string
    {
        $csvContent = $this->twig->render($templatePath, $data);

        $publicPath = $this->projectDir . '/public/generated_reports/';
        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0777, true);
        }
        $filePath = $publicPath . $outputFilename;

        file_put_contents($filePath, $csvContent);

        return str_replace($this->projectDir . '/public/', '', $filePath);
    }
}
