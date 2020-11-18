<?php

namespace App\EventListener;

use App\Entity\User\User;
use App\Resolver\UserResolver;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

/**
 * Class AuthenticationSuccessListener.
 */
class AuthenticationSuccessListener
{
    /**
     * @var UserResolver
     */
    private UserResolver $userResolver;

    /**
     * AuthenticationSuccessListener constructor.
     *
     * @param UserResolver $userResolver
     */
    public function __construct(UserResolver $userResolver)
    {
        $this->userResolver = $userResolver;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();

        /** @var User $user */
        $user = $event->getUser();
        $data += $this->userResolver->getExtraData($user);

        $event->setData($data);
    }
}
