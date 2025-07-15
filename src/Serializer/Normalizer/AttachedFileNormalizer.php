<?php

namespace App\Serializer;

use App\Entity\InstrumentTheme;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Vich\UploaderBundle\Storage\StorageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class InstrumentThemeNormalizer implements NormalizerInterface
{
    const ALREADY_NORMALIZED = 'instrument_theme_already_normalized';
    
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
     * @var InstrumentTheme $data
     */
    public function normalize($data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $normalized = $this->normalizer->normalize($data, $format, $context);

        if (isset($normalized['frontImage'])) {
            $frontImage = $this->requestStack->getCurrentRequest()->getUriForPath($this->storage->resolveUri($data, 'frontImageFile'));
            $normalized['frontImage'] = $frontImage;
        }

        if (isset($normalized['backImage'])) {
            $backImage = $this->requestStack->getCurrentRequest()->getUriForPath($this->storage->resolveUri($data, 'backImageFile'));
            $normalized['backImage'] = $backImage;
        }

        return $normalized;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof InstrumentTheme;
    }

    public function getSupportedTypes(?string $format = null): array
    {
        return [
            InstrumentTheme::class => true
        ];
    }
}