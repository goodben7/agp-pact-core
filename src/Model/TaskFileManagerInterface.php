<?php

namespace App\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface TaskFileManagerInterface
{
    public function save(UploadedFile $file, string $directory): string;

    public function getPath(string $fileId, string $directory): string;

    public function getSize(string $fileId, string $directory): int;
}