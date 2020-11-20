<?php

namespace App\Assembler;

use App\Dto\DtoInterface;
use App\Dto\PaginatedDto;
use App\Serializer\JsonSerializer;
use Pagerfanta\Pagerfanta;

/**
 * Class AbstractAssembler.
 */
abstract class AbstractAssembler
{
    /**
     * @var JsonSerializer
     */
    protected JsonSerializer $serializer;

    /**
     * AbstractAssembler constructor.
     *
     * @param JsonSerializer $serializer
     */
    public function __construct(
        JsonSerializer $serializer
    ) {
        $this->serializer            = $serializer;
    }

    /**
     * @param $entity
     *
     * @return DtoInterface
     */
    abstract public function transform($entity): DtoInterface;

    /**
     * @param DtoInterface $dto
     * @param null         $entity
     *
     * @throws \Exception
     */
    abstract public function reverseTransform(DtoInterface $dto, $entity = null);

    /**
     * @param $entity
     * @param array $params
     *
     * @return DtoInterface
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function transformAndPatch($entity, array $params): DtoInterface
    {
        $dto = $this->transform($entity);

        $this->serializer->denormalize($params, get_class($dto), $dto);

        return $dto;
    }

    /**
     * @param $entities
     *
     * @return array
     */
    public function transformArray($entities): array
    {
        $dtos = [];

        foreach ($entities as $entity) {
            $dtos[] = $this->transform($entity);
        }

        return $dtos;
    }

    /**
     * @param Pagerfanta $pagerfanta
     *
     * @return PaginatedDto
     */
    public function transformPagerfanta(Pagerfanta $pagerfanta): PaginatedDto
    {
        return new PaginatedDto($pagerfanta, $this->transformArray($pagerfanta->getCurrentPageResults()));
    }
}