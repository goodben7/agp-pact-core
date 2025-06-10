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

        $parsedRequestData = array_map(static function ($element) {
            if (is_string($element)) {
                $decoded = json_decode($element, true);
                return \is_array($decoded) ? $decoded : $element;
            }
            return $element;
        }, $request->request->all());

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
