<?php

namespace App\Manager;

use App\Model\TaskFileManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class TaskFileManager implements TaskFileManagerInterface
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function save(UploadedFile $file, string $directory): string
    {
        $targetDir = "{$this->projectDir}/{$directory}";
        $task = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        $file->move($targetDir, $task);
        return $task;
    }

    public function getPath(string $fileId, string $directory): string
    {
        return "{$this->projectDir}/{$directory}/{$fileId}";
    }

    public function getSize(string $fileId, string $directory): int
    {
        $file = new File($this->getPath($fileId, $directory));
        return $file->getSize();
    }
}
