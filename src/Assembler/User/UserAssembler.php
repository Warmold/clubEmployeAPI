<?php

namespace App\Assembler\User;

use App\Assembler\AbstractAssembler;
use App\Dto\DtoInterface;
use App\Dto\Invitation\InvitationDto;
use App\Dto\User\UserDto;
use App\Entity\Invitation\Invitation;
use App\Entity\User\User;
use App\Manager\Invitation\InvitationManager;
use App\Manager\User\UserManager;
use App\Serializer\JsonSerializer;


class UserAssembler extends AbstractAssembler
{

    private UserManager $userManager;


    public function __construct(UserManager $userManager, JsonSerializer $serializer)
    {
        $this->userManager = $userManager;

        parent::__construct($serializer);
    }

    /**
     * @param $user
     *
     * @return DtoInterface
     */
    public function transform($user): DtoInterface
    {
        if (!$user instanceof User) {
            throw new \TypeError(sprintf(
                'Argument 1 passed to %s() must be an instance of %s, %s given.',
                __METHOD__,
                UserDto::class,
                \is_object($user) ? \get_class($user) : \gettype($user)
            ));
        }

        $userDto                          = new UserDto();
        $userDto->uuid                    = $user->getUuid();
        $userDto->firstname               = $user->getFirstname();
        $userDto->lastname                = $user->getLastname();
        $userDto->gender                  = $user->getGender();
        $userDto->email                   = $user->getEmail();
        $userDto->createdAt               = $user->getCreatedAt();
        $userDto->updatedAt               = $user->getUpdatedAt();
        $userDto->roles                   = $user->getRoles();

        return $userDto;;
    }

    /**
     * @param DtoInterface $userDto
     * @param User|null    $currentUser
     *
     * @throws \Exception
     *
     * @return User
     */
    public function reverseTransform(DtoInterface $userDto, $currentUser = null): User
    {
        if (!$userDto instanceof UserDto) {
            throw new \TypeError(sprintf(
                'Argument 1 passed to %s() must be an instance of %s, %s given.',
                __METHOD__,
                UserDto::class,
                \is_object($userDto) ? \get_class($userDto) : \gettype($userDto)
            ));
        }

        return $user;
    }
}
