<?php

namespace App\Serializer\Normalizer;

use App\Entity\AttachedFile;
use App\Entity\Member;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Vich\UploaderBundle\Storage\StorageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class MemberFileNormalizer implements NormalizerInterface
{
    const ALREADY_NORMALIZED = 'member_already_normalized';

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
     * @var Member $data
     */
    public function normalize($data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::ALREADY_NORMALIZED] = true;
        $normalized = $this->normalizer->normalize($data, $format, $context);

        $normalized['profilePicture'] = null;

        if ($data->getProfilePicture()) {
            $path = $this->storage->resolveUri($data, 'profilePictureFile');
            $request = $this->requestStack->getCurrentRequest();

            if ($path && $request) {
                $normalized['profilePicture'] = $request->getUriForPath($path);
            }
        }

        return $normalized;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return !isset($context[self::ALREADY_NORMALIZED]) && $data instanceof Member;
    }

    public function getSupportedTypes(?string $format = null): array
    {
        return [
            Member::class => true
        ];
    }
}
