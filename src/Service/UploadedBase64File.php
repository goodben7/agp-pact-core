<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadedBase64File
{
    public function handleBase64Image(string $base64Image): UploadedFile
    {
        $imageData = base64_decode($base64Image);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'kyc_');
        file_put_contents($tempFilePath, $imageData);

        $file = new File($tempFilePath);
        
        $mimeType = $file->getMimeType();
        $extension = $this->getExtensionFromMimeType($mimeType);

        $newFilePath = "{$tempFilePath}.{$extension}";
        rename($tempFilePath, $newFilePath);

        $uploadedFile = new UploadedFile(
            $newFilePath, 
            pathinfo($newFilePath, PATHINFO_BASENAME),
            $mimeType,
            null,
            true 
        );

        return $uploadedFile;
    }

    private function getExtensionFromMimeType(string $mimeType): string
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
        ];

        return $extensions[$mimeType] ?? 'bin';
    }
}