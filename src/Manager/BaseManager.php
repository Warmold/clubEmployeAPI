<?php

namespace App\Manager;

use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;

class BaseManager
{
    /**
     * @var EntityManagerInterface
     *
     * @required
     */
    public EntityManagerInterface $em;

    /**
     * @var RequestStack
     *
     * @required
     */
    public RequestStack $requestStack;

    /**
     * @var ObjectRepository|object
     */
    protected $entityRepository;

    /**
     * BaseManager constructor.
     *
     * @param EntityManagerInterface    $em
     * @param ObjectRepository          $entityRepository
     */
    public function __construct(EntityManagerInterface $em, ObjectRepository $entityRepository = null)
    {
        $this->em               = $em;
        $this->entityRepository = $entityRepository;
    }

    /**
     * @param int         $page
     * @param int         $perPage
     * @param array       $options
     * @param string|null $locale
     *
     * @return Pagerfanta
     */
    public function getPagerfanta($page = 1, $perPage = 10, $options = [], ?string $locale = null): Pagerfanta
    {
        if (!method_exists($this, 'getPagerfantaQueryBuilder')) {
            throw new \LogicException('getPagerfantaQueryBuilder method not exist');
        }

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getPagerfantaQueryBuilder($options);
        $query        = $queryBuilder->getQuery();

        $adapter    = new DoctrineORMAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setMaxPerPage($perPage);
        $pagerfanta->setCurrentPage($page);

        return $pagerfanta;
    }

    /**
     * @param $entity
     *
     * @return mixed
     */
    public function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    /**
     * @param $entity
     */
    public function delete($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * @param $entity
     */
    public function remove($entity)
    {
        $this->em->remove($entity);
    }

    /**
     * @param $entity
     */
    public function persist($entity)
    {
        $this->em->persist($entity);
    }

    /**
     * @return void
     */
    public function flush()
    {
        $this->em->flush();
    }

    /**
     * @return void
     */
    public function clear()
    {
        $this->em->clear();
    }
}
