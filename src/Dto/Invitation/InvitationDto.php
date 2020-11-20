<?php


namespace App\Dto\Invitation;

use Symfony\Component\Serializer\Annotation\Groups;
use App\Dto\DtoInterface;
use Swagger\Annotations as SWG;

class InvitationDto implements DtoInterface
{
    /**
     * @var string|null
     *
     * @Groups({"all"})
     */
    public $uuid;

    /**
     * @var string|null
     *
     * @Groups({"invitation_all"})
     */
    public $title;

    /**
     * @var string|null
     *
     * @Groups({"invitation_all"})
     */
    public $content;

    /**
     * @SWG\Property(type="string")
     * @Groups({"invitation_all"})
     */
    public $sender;

    /**
     * @SWG\Property(type="string")
     * @Groups({"invitation_all"})
     */
    public $guest;

    /**
     * @var \DateTimeInterface|null
     *
     * @Groups({"invitation_all"})
     */
    public $invitedAt;

    /**
     * @var string|null
     *
     * @Groups({"invitation_all"})
     */
    public $state;

    /**
     * @var \DateTimeInterface|null
     *
     * @Groups({"private_all"})
     */
    public $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Groups({"private_all"})
     */
    public ?\DateTimeInterface $updatedAt;


}