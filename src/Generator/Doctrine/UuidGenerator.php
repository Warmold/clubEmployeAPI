<?php

namespace App\Generator\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\Entity;
use Ramsey\Uuid\Doctrine\UuidGenerator as RamseyUuidGenerator;

/**
 * Class UuidGenerator.
 */
class UuidGenerator implements EventSubscriber
{
    /**
     * @var string
     */
    public const UUID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';

    /**
     * @var RamseyUuidGenerator
     */
    private RamseyUuidGenerator $generator;

    /**
     * @param RamseyUuidGenerator $uuidGenerator
     */
    public function __construct(RamseyUuidGenerator $uuidGenerator)
    {
        $this->generator = $uuidGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->updateUuid($args);
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @return bool
     */
    public function updateUuid(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!property_exists(get_class($entity), 'uuid')) {
            return false;
        }

        if (empty($entity->getUuid())) {
            /** @var Entity $entity */
            $uuid = $this->generator->generate($args->getEntityManager(), $entity);
            $entity->setUuid($uuid);
        }

        return true;
    }
}