<?php


namespace App\Manager\Invitation;

use App\Entity\Invitation\Invitation;
use App\Entity\User\User;
use App\Manager\BaseManager;
use App\Repository\InvitationRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class InvitationManager
 */
class InvitationManager extends BaseManager
{
    /**
     * @var InvitationRepository|Object
     */
    protected $entityRepository;

    /**
     * InvitaionManager constructor.
     *
     * @param EntityManagerInterface $em
     * @param InvitationRepository $invitationRepository
     */
    public function __construct(EntityManagerInterface $em, InvitationRepository $invitationRepository)
    {
        parent::__construct($em, $invitationRepository);
    }

    /**
     * @param string $uuid
     *
     * @return Invitation|object|null
     */
    public function findOneByUuid(User $user): ? User
    {
        return $this->entityRepository->getMyInvitation($user);
    }

    /**
     * @param string $uuid
     *
     * @return Invitation|object|null
     */
    public function findAllByUser(User $user): array
    {
        return $this->entityRepository->findAllByUser($user);
    }

    /**
     * @param array $options
     *
     * @return mixed
     */
    public function getPagerfantaQueryBuilder($options = [])
    {
        return $this->entityRepository->getInvitationQueryBuilder($options);
    }


}