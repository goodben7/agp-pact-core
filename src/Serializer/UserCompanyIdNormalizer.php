<?php

namespace App\Serializer;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

/**
 * Normalizer to add companyId to User API responses
 */
class UserCompanyIdNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'USER_COMPANY_ID_NORMALIZER_ALREADY_CALLED';

    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);
        
        if (is_array($data) && $object instanceof User && $object->getId() !== null) {
            // Add companyId to the normalized data
            $data['companyId'] = $this->userRepository->findCompanyIdByUserId($object->getId());
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        // Make sure we're not called twice
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof User;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [User::class => false];
    }
}