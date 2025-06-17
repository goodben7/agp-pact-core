<?php

namespace App\Service\Generator;

use Twig\Environment;
use Dompdf\Dompdf;
use Dompdf\Options;

readonly class PdfGeneratorService
{
    public function __construct(
        private Environment $twig,
        private string      $projectDir
    )
    {
    }

    /**
     * Génère un fichier PDF à partir d'un template Twig et de données.
     *
     * @param string $templatePath Le chemin du template Twig (ex: 'reports/complaint_summary_report.html.twig').
     * @param array $data Les données à passer au template Twig.
     * @param string $outputFilename Le nom du fichier de sortie (sans chemin).
     * @return string Le chemin complet du fichier PDF généré.
     * @throws \Exception Si la génération échoue.
     */
    public function generateFromHtmlTemplate(string $templatePath, array $data, string $outputFilename): string
    {
        $html = $this->twig->render($templatePath, $data);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Pour charger des images externes si nécessaire

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $publicPath = $this->projectDir . '/public/generated_reports/';
        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0777, true);
        }
        $filePath = $publicPath . $outputFilename;

        file_put_contents($filePath, $dompdf->output());

        return str_replace($this->projectDir . '/public/', '', $filePath); // Retourne le chemin relatif au dossier public
    }
}
