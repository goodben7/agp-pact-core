<?php

namespace App\Serializer\Normalizer;

use App\Entity\AttachedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Vich\UploaderBundle\Storage\StorageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class AttachedFileNormalizer implements NormalizerInterface
{
    const ALREADY_NORMALIZED = 'attached_file_already_normalized';

    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer,
        private StorageInterface    $storage,
        private RequestStack        $requestStack
    )
    {
    }

    /**
     * @throws ExceptionInterface
     * @var AttachedFile $data
     */
    public function normalize($data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $normalized = $this->normalizer->normalize($data, $format, $context);

        $file = $this->requestStack->getCurrentRequest()->getUriForPath($this->storage->resolveUri($data, 'file'));
        $normalized['filePath'] = $file;

        return $normalized;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof AttachedFile;
    }

    public function getSupportedTypes(?string $format = null): array
    {
        return [
            AttachedFile::class => true
        ];
    }
}
