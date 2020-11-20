<?php


namespace App\Entity\Invitation;

use App\Entity\Traits\EntityIdTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Invitation\InvitationRepository")
 * @ORM\Table (
 *     name="invitation",
 *     indexes={
 *         @ORM\Index(name="state", columns={"state"}),
 *     },
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="uuid", columns={"uuid"}),
 *    }
 * )
 */
class Invitation
{
    use EntityIdTrait;
    use TimestampableTrait;

    // invitation state
    public const STATE_PENDING  = "pending";
    public const STATE_ACCEPTED = "accepted";
    public const STATE_REFUSED  = "refused";
    public const STATE_CANCELED = "canceled";


    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User", inversedBy="senders")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id", nullable=false)
     */
    private ?User $sender = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User", inversedBy="guests")
     * @ORM\JoinColumn(name="guest_id", referencedColumnName="id", nullable=false)
     */
    private ?User $guest = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $invitedAt;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private ?string $state = self::STATE_PENDING;

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return self
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     *
     * @return self
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param  $sender
     *
     * @return Invitation
     */
    public function setSender($sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGuest()
    {
        return $this->guest;
    }

    /**
     * @param  $guest
     * @return Invitation
     */
    public function setGuest($guest): self
    {
        $this->guest = $guest;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getInvitedAt(): ?\DateTimeInterface
    {
        return $this->invitedAt;
    }

    /**
     * @param \DateTimeInterface|null $invitedAt
     */
    public function setInvitedAt(?\DateTimeInterface $invitedAt): void
    {
        $this->invitedAt = $invitedAt;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
     *
     * @return self
     */
    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }
}