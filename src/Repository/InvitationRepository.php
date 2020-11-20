<?php


namespace App\Repository;


use App\Entity\Invitation\Invitation;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class InvitationRepository extends ServiceEntityRepository
{
    /**
     * PageRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invitation::class);
    }

    public function getInvitationQueryBuilder() :QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('i');

        return $queryBuilder;

    }

    public function findAllByUser(User $user) {
        $queryBuilder = $this->createQueryBuilder('i')
            ->where('u.user = :user')
            ->setParameter('user', $user);

        return $queryBuilder->getQuery()->getResult();
    }

   /* public function getGuestInvitaion(Invitation $invitation){

    }*/

}