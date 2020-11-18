<?php

namespace App\Dto\User;

use App\Dto\DtoInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserDto implements DtoInterface
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
     * @Groups({"user_all"})
     */
    public ?string $firstname = null;

    /**
     * @var string|null
     *
     * @Groups({"user_all"})
     */
    public ?string $lastname = null;

    /**
     * @var string|null
     *
     * @Assert\Choice(choices={"m", "f", "o"})
     *
     * @Groups({"user_all"})
     */
    public ?string $gender = null;

    /**
     * @var string|null
     *
     * @Groups({"user_all"})
     */
    public ?string $email = null;

    /**
     * @var string|null
     *
     * @Groups({"registration"})
     */
    public ?string $password = null;

    /**
     * @var string|null
     *
     * @Groups({"user_all"})
     */
    public ?string $token = null;

    /**
     * @var array
     *
     * @Groups({"user_all"})
     */
    public array $roles = [];

    /**
     * @var \DateTimeInterface|null
     *
     * @Groups({"user_private"})
     */
    public ?\DateTimeInterface $createdAt = null;

    /**
     * @var \DateTimeInterface|null
     *
     * @Groups({"user_private"})
     */
    public ?\DateTimeInterface $updatedAt = null;
}
