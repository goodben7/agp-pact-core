<?php

namespace App\Encoder;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class MultipartDecoder implements DecoderInterface
{
    public const FORMAT = 'multipart';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface $logger
    ) {
    }

    public function decode(string $data, string $format, array $context = []): ?array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            $this->logger->warning('MultipartDecoder: No current request found');
            return null;
        }

        // Log raw request data for debugging
        $this->logger->info('MultipartDecoder: Processing request', [
            'content_type' => $request->headers->get('Content-Type'),
            'method' => $request->getMethod(),
            'request_data_keys' => array_keys($request->request->all()),
            'files_keys' => array_keys($request->files->all()),
        ]);

        // Process form data
        $parsedRequestData = [];
        foreach ($request->request->all() as $key => $element) {
            if (is_string($element)) {
                // Try to decode JSON, but keep original if it fails
                $decoded = json_decode($element, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $parsedRequestData[$key] = $decoded;
                } else {
                    $parsedRequestData[$key] = $element;
                }
            } else {
                $parsedRequestData[$key] = $element;
            }
        }

        // Process files
        $parsedFiles = [];
        foreach ($request->files->all() as $key => $file) {
            if (is_array($file)) {
                // Handle multiple files
                $validFiles = array_filter($file, static fn($f) => $f instanceof UploadedFile && $f->isValid());
                if (!empty($validFiles)) {
                    $parsedFiles[$key] = $validFiles;
                }
            } elseif ($file instanceof UploadedFile && $file->isValid()) {
                $parsedFiles[$key] = $file;
            }
        }

        $result = array_merge($parsedRequestData, $parsedFiles);

        $this->logger->info('MultipartDecoder: Decoded data', [
            'parsed_keys' => array_keys($result),
            'file_keys' => array_keys($parsedFiles),
        ]);

        return $result;
    }

    public function supportsDecoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}
