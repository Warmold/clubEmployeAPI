<?php


namespace App\Assembler\Invitation;

use App\Assembler\AbstractAssembler;
use App\Assembler\User\UserAssembler;
use App\Dto\DtoInterface;
use App\Dto\Invitation\InvitationDto;
use App\Entity\Invitation\Invitation;
use App\Entity\User\User;
use App\Manager\Invitation\InvitationManager;
use App\Manager\User\UserManager;
use App\Serializer\JsonSerializer;
use Symfony\Component\Security\Core\Security;

/**
 * Class InvitationAssembler
 */
class InvitationAssembler extends AbstractAssembler
{

    /**
     * @var InvitationManager
     */
    private InvitationManager $invitationManager;

    /**
     * @var UserAssembler
     */
    private UserAssembler $userAssembler;

    private UserManager $userManager;

    /**
     * InvitationAssembler constructor.
     *
     * @param InvitationManager $invitationManager
     * @param JsonSerializer $serializer
     */
    public function __construct(InvitationManager $invitationManager,
                                UserAssembler $userAssembler,
                                UserManager $userManager,
                                JsonSerializer $serializer)
    {
        $this->invitationManager = $invitationManager;
        $this->userAssembler     = $userAssembler;
        $this->userManager       = $userManager;

        parent::__construct($serializer);
    }

    /**
     * @param   $invitation
     * @return  DtoInterface
     */
    public function transform($invitation): DtoInterface
    {
        if (!$invitation instanceof Invitation) {
            throw new \TypeError(sprintf(
                'Argument 1 passed to %s() must be an instance of %s, %s given.',
                __METHOD__,
                InvitationDto::class,
                \is_object($invitation) ? \get_class($invitation) : \gettype($invitation)
            ));
        }

        $invitationDto = new InvitationDto();

        $invitationDto->uuid        = $invitation->getUuid();
        $invitationDto->title       = $invitation->getTitle();
        $invitationDto->content     = $invitation->getContent();
        $invitationDto->sender      = $invitation->getSender();
        $invitationDto->state       = $invitation->getState();
        $invitationDto->createdAt   = $invitation->getCreatedAt();
        $invitationDto->updatedAt   = $invitation->getUpdatedAt();

        if ($guest = $invitation->getGuest()) {
            $invitationDto->guest   = $this->userAssembler->transform($guest);
        }

        return $invitationDto;
    }

    public function reverseTransform(DtoInterface $invitationDto, $invitation = null): Invitation
    {
        if (!$invitationDto instanceof InvitationDto) {
            throw new \TypeError(sprintf(
                'Argument 1 passed to %s() must be an instance of %s, %s given.',
                __METHOD__,
                InvitationDto::class,
                \is_object($invitationDto) ? \get_class($invitationDto) : \gettype($invitationDto)
            ));
        }
        $invitation = $invitation ?? new Invitation();

        $invitation->setTitle($invitationDto->title)
            ->setContent($invitationDto->content)
            ->setInvitedAt($invitationDto->invitedAt);

        $user = $this->userManager->findUserByUuid($invitationDto->guest);

        $invitation->setGuest($user);

        return $invitation;
    }
}
