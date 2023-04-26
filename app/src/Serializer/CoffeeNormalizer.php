<?php

namespace App\Serializer;

use App\Entity\Coffee;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CoffeeNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function __construct(
        private AbstractNormalizer $normalizer,
    ) {
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        return $data;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $object = $this->normalizer->denormalize($data, $type, $format, $context);

        return $object;
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof Coffee;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return Coffee::class === $type;
    }
}
