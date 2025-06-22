<?php

namespace App\Encoder;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class MultipartDecoder implements DecoderInterface
{
    public const FORMAT = 'multipart';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function decode(string $data, string $format, array $context = []): ?array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return null;
        }

        $parsedRequestData = [];
        foreach ($request->request->all() as $key => $value) {
            if (is_string($value) && (str_starts_with($value, '{') || str_starts_with($value, '['))) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $parsedRequestData[$key] = $decoded;
                } else {
                    $parsedRequestData[$key] = $value;
                }
            } else {
                $parsedRequestData[$key] = $value;
            }
        }

        $parsedFiles = array_map(static function ($file) {
            if (\is_array($file)) {
                return array_filter($file, static fn($f) => $f instanceof UploadedFile && $f->isValid());
            }
            return $file instanceof UploadedFile && $file->isValid() ? $file : null;
        }, $request->files->all());

        return $parsedRequestData + $parsedFiles;
    }

    public function supportsDecoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}
