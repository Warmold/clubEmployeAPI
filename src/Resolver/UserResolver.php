<?php

namespace App\Resolver;

use App\Entity\User\User;
use App\Manager\User\UserManager;

/**
 * Class UserResolver
 */
class UserResolver
{
    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * AuthenticationSuccessListener constructor.
     *
     * @param UserManager    $userManager
     *
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager    = $userManager;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getExtraData(User $user)
    {
        $data = [
            'uuid'              => $user->getUuid(),
            'roles'             => $user->getRoles(),
            'firstname'         => $user->getFirstname(),
            'lastname'          => $user->getLastname(),
            'email'             => $user->getEmail(),
        ];

        return $data;
    }
}
