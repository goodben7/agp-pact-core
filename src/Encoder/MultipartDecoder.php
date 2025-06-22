<?php

namespace App\Encoder;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

// Added for explicit exception

class MultipartDecoder implements DecoderInterface
{
    public const FORMAT = 'multipart';

    public function __construct(
        private readonly RequestStack    $requestStack,
        private readonly LoggerInterface $logger // Injected for logging
    )
    {
    }

    /**
     * {@inheritdoc}
     *
     * Decodes the multipart/form-data request into an associative array.
     * The original $data parameter is ignored as data comes from RequestStack ($_POST and $_FILES).
     *
     * @param string $data The raw request body (ignored).
     * @param string $format The format the data is in (expected to be 'multipart').
     * @param array<string, mixed> $context Options for the decoder.
     * @return array<string, mixed> The decoded data, combining form fields and uploaded files.
     * @throws UnexpectedValueException If no current request is available.
     */
    public function decode(string $data, string $format, array $context = []): array // Changed return type to array, not ?array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            $this->logger->error('MultipartDecoder: No current request found. Cannot decode multipart data.');
            // Throw an exception as a missing request means the decoder cannot fulfill its purpose.
            throw new UnexpectedValueException('No current request available to decode multipart data.');
        }

        $this->logger->info('MultipartDecoder: Decoding multipart request.', [
            'method' => $request->getMethod(),
            'content_type' => $request->headers->get('Content-Type'),
            'request_uri' => $request->getUri(),
        ]);

        // --- Process regular form data (from $_POST) ---
        $parsedRequestData = [];
        foreach ($request->request->all() as $key => $element) {
            if (is_string($element)) {
                // Attempt to JSON decode strings. This is useful if your frontend stringifies
                // objects/arrays into form fields (e.g., for nested DTOs or complex inputs).
                $decoded = json_decode($element, true);
                if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_scalar($decoded))) {
                    // Successfully decoded to an array, or a scalar (like true/false/number as string)
                    $parsedRequestData[$key] = $decoded;
                    $this->logger->debug(sprintf('MultipartDecoder: JSON decoded form field "%s".', $key));
                } else {
                    // Not valid JSON, or JSON decoded to null (e.g. "null" string)
                    // Keep the original string.
                    $parsedRequestData[$key] = $element;
                    $this->logger->debug(sprintf('MultipartDecoder: Form field "%s" is a plain string.', $key));
                }
            } else {
                // It's already an array, boolean, integer etc.
                $parsedRequestData[$key] = $element;
                $this->logger->debug(sprintf('MultipartDecoder: Form field "%s" is non-string data.', $key));
            }
        }
        $this->logger->info('MultipartDecoder: Processed form fields.', ['keys' => array_keys($parsedRequestData)]);

        // --- Process uploaded files (from $_FILES) ---
        $parsedFiles = [];
        foreach ($request->files->all() as $key => $file) {
            if (is_array($file)) {
                // Handle multiple files under the same field name (e.g., <input type="file" name="attachments[]">)
                // Filter out non-UploadedFile instances or invalid uploads
                $validFiles = array_filter($file, static fn($f) => $f instanceof UploadedFile && $f->isValid());
                if (!empty($validFiles)) {
                    $parsedFiles[$key] = array_values($validFiles); // Re-index array if some files were filtered
                    $this->logger->debug(sprintf('MultipartDecoder: Processed multiple files for "%s". Count: %d', $key, count($validFiles)));
                } else {
                    $this->logger->debug(sprintf('MultipartDecoder: No valid files found for multiple upload field "%s".', $key));
                }
            } elseif ($file instanceof UploadedFile && $file->isValid()) {
                // Handle a single uploaded file
                $parsedFiles[$key] = $file;
                $this->logger->debug(sprintf('MultipartDecoder: Processed single file for "%s".', $key));
            } else {
                // Log invalid or missing file attempts
                $this->logger->warning(sprintf('MultipartDecoder: Invalid or no file uploaded for field "%s".', $key), ['file_data' => $file]);
            }
        }
        $this->logger->info('MultipartDecoder: Processed uploaded files.', ['keys' => array_keys($parsedFiles)]);

        // --- Combine all parsed data ---
        // array_merge will prioritize $parsedFiles if a key exists in both $parsedRequestData and $parsedFiles.
        // This is generally desired for file uploads where the file might replace a placeholder string.
        $result = array_merge($parsedRequestData, $parsedFiles);

        $this->logger->info('MultipartDecoder: Final decoded data structure.', [
            'final_keys' => array_keys($result),
            'data_types' => array_map(static fn($v) => get_debug_type($v), $result),
        ]);

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $format The format to decode from.
     * @return bool Whether the decoder supports the given format.
     */
    public function supportsDecoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}
