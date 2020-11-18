<?php

namespace App\Manager\User;

use App\Entity\User\User;
use App\Manager\BaseManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;


class UserManager extends BaseManager
{
    /**
     * @var UserRepository|ObjectRepository
     */
    protected $entityRepository;

    /**
     * UserManage constructor.
     *
     * @param UserRepository            $userRepository
     * @param EntityManagerInterface    $em
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        parent::__construct($em, $userRepository);
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->entityRepository->findAll();
    }

    /**
     * @param string $uuid
     *
     * @return User|null
     */
    public function findUserByUuid(string $uuid): ?User
    {
        return $this->entityRepository->findOneBy(['uuid' => $uuid]);
    }

    /**
     * @param array $options
     *
     * @return mixed
     */
    public function getPagerfantaQueryBuilder($options = [])
    {
        return $this->entityRepository->getUsersQueryBuilder($options);
    }
}