<?php

namespace App\Generator;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Uid\UuidV4;

/**
 * Class UuidGenerator
 */
class UuidGenerator extends AbstractIdGenerator
{
    /**
     * @var string
     */
    public const UUID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';

    /**
     * Generate an identifier
     *
     * @param EntityManager $em
     * @param Entity        $entity
     *
     * @return UuidV4
     *
     * @throws \Exception
     */
    public function generate(EntityManager $em, $entity): UuidV4
    {
        return new UuidV4();
    }

}