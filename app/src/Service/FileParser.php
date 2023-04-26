<?php

namespace App\Service;

use App\Entity\Coffee;
use App\Exception\AppException;
use App\Exception\FileNotFoundException;
use App\Exception\UnsupportedFileTypeException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FileParser implements DataParserInterface
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly array $encoders,
        private readonly string $fileName)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader());
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);

        $this->serializer = new Serializer(
            [new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter), new ArrayDenormalizer()],
            $encoders
        );

        $this->validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();
    }

    /**
     * @param string $source data source
     *
     * @return Coffee[] of deserialized Coffee objects
     *
     * @throws AppException
     */
    public function parse(): array
    {
        $format = pathinfo($this->fileName)['extension'];

        if (!is_file($this->fileName)) {
            throw new FileNotFoundException(['filename' => $this->fileName]);
        }

        if (!$this->serializer->supportsEncoding($format)) {
            throw new UnsupportedFileTypeException(['filename' => $this->fileName]);
        }

        $data = file_get_contents($this->fileName);
        try {
            $array = $this->serializer->decode($data, $format);
        } catch (NotEncodableValueException $e) {
            throw new AppException($e->getMessage(), ['filename' => $this->fileName]);
        }

        $objects = [];

        foreach ($array as $item) {
            /* @var Coffee $coffee */
            try {
                $coffee = $this->serializer->denormalize($item, Coffee::class);
                $violations = $this->validator->validate($coffee);
                if ($violations->count() > 0) {
                    $this->logger->error('validation error', ['item' => $item]);
                    continue;
                }
                $objects[] = $coffee;
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['item' => $item]);
            }
        }

        return $objects;
    }
}
