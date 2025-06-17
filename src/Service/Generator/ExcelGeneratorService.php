<?php

namespace App\Service\Generator;

use Twig\Environment;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

readonly class ExcelGeneratorService
{
    public function __construct(
        private string $projectDir
    )
    {
    }

    /**
     * Génère un fichier Excel (XLSX) à partir de données.
     * Pour une approche simple, on peut itérer sur les données.
     * Pour des templates complexes, on pourrait utiliser un template Twig pour générer un XML compatible XLSX.
     *
     * @param array $data Les données structurées pour le rapport (ex: reportData.results).
     * @param string $outputFilename Le nom du fichier de sortie (sans chemin).
     * @return string Le chemin complet du fichier XLSX généré.
     * @throws \Exception Si la génération échoue.
     */
    public function generateFromData(array $data, string $outputFilename): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (isset($data['headers']) && is_array($data['headers'])) {
            $sheet->fromArray($data['headers'], null, 'A1');
        }

        if (isset($data['results']) && is_array($data['results'])) {
            $rowNum = (isset($data['headers']) && is_array($data['headers'])) ? 2 : 1;
            foreach ($data['results'] as $rowData) {
                $sheet->fromArray(array_values($rowData), null, 'A' . $rowNum);
                $rowNum++;
            }
        }

        $writer = new Xlsx($spreadsheet);
        $publicPath = $this->projectDir . '/public/generated_reports/';
        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0777, true);
        }
        $filePath = $publicPath . $outputFilename;

        $writer->save($filePath);

        return str_replace($this->projectDir . '/public/', '', $filePath);
    }
}
