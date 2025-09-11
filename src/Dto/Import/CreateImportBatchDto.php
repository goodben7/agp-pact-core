<?php

namespace App\Dto\Import;

use App\Entity\ImportMapping;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class CreateImportBatchDto
{
    #[Assert\NotNull(message: 'Le fichier à importer est requis.')]
    #[Assert\File(
        maxSize: '10M',
        mimeTypes: [
            'text/csv',
            'text/plain',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ],
        mimeTypesMessage: 'Veuillez téléverser un fichier CSV ou Excel valide.'
    )]
    public ?File $file = null;

    #[Assert\NotNull(message: "Le modèle d'importation est requis.")]
    public ?ImportMapping $mapping = null;
}