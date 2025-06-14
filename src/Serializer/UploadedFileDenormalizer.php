<?php

namespace App\Serializer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UploadedFileDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $type, ?string $format = null, array $context = []): mixed
    {
        return $data;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return $data instanceof UploadedFile && ($type === File::class || $type === UploadedFile::class);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            File::class => true,
            UploadedFile::class => true,
        ];
    }
}