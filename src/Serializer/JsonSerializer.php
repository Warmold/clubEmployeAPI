<?php

namespace App\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ConstraintViolationListNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class JsonSerializer.
 */
class JsonSerializer
{
    /**
     * @var Serializer
     */
    private Serializer $serializer;


    /**
     * JsonSerializer constructor.
     */
    public function __construct()
    {
        $encoders             = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers          = [
            new DateTimeNormalizer(),
            new ObjectNormalizer($classMetadataFactory, new CamelCaseToSnakeCaseNameConverter(), null, new PhpDocExtractor()),
            new ConstraintViolationListNormalizer([], new CamelCaseToSnakeCaseNameConverter()),
            new ArrayDenormalizer(),
        ];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @param $data
     *
     * @return bool|float|int|string
     */
    public function serialize($data, $contexts = [])
    {
        return $this->serializer->serialize($data, 'json');
    }

    /**
     * @param $data
     * @param $type
     * @param null $object
     *
     * @return object
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function denormalize($data, $type, $object = null)
    {
        return $this->serializer->denormalize($data, $type, 'json', [
            AbstractObjectNormalizer::OBJECT_TO_POPULATE      => $object,
            AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true,
        ]);
    }
}