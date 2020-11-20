<?php

namespace App\Entity\User;

use App\Entity\Product\Product;
use App\Entity\Traits\EntityIdTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Generator\WeggGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="user",
 *     indexes={
 *          @ORM\Index(name="username", columns={"username"}),
 *    },
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="uuid", columns={"uuid"}),
 *          @ORM\UniqueConstraint(name="email", columns={"email"}),
 *    }
 * )
 * @UniqueEntity(fields="email")
 */
class User implements UserInterface
{
    use EntityIdTrait;
    use TimestampableTrait;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private ?string $username;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $password;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $salt = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $firstname = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $lastname = null;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private ?string $gender = null;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Invitation\Invitation",
     *     mappedBy="sender",
     *     cascade={"persist", "remove"}
     *     ))
     */
    private $senders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Invitation\Invitation",
     *      mappedBy="guest",
     *      cascade={"persist", "remove"}
     *     ))
     */
    private $guests;

    /**
     * User constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->guests    = new ArrayCollection();
        $this->senders   = new ArrayCollection();
        $this->roles     = ['ROLE_USER'];
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     *
     * @return self
     */
    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     *
     * @return self
     */
    public function setEmail(?string $email): self
    {
        $this->setUsername($email);
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @param string|null $salt
     *
     * @return self
     */
    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     *
     * @return self
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     *
     * @return self
     */
    public function setFirstname(?string $firstname = null): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     *
     * @return self
     */
    public function setLastname(?string $lastname = null): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string|null $gender
     *
     * @return self
     */
    public function setGender(?string $gender = null): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return \array_unique($this->roles);
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function addRole(string $role): self
    {
        $role = strtoupper($role);

        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        $this->addRole('ROLE_CUSTOMER')
            ->addRole('ROLE_USER');

        return $this;
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return \in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * Required by UserInterface.
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return sprintf('%s %s', $this->getFirstname(), $this->getLastname());
    }
}