<?php

namespace App\EventListener\Lifecycle;

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
     * @var RamseyUuidGenerator
     */
    private $generator;

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
    public function getSubscribedEvents(): array
    {
        return [
            \Doctrine\ORM\Events::prePersist,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->updateUuid($args);
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function updateUuid(LifecycleEventArgs $args): bool
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
