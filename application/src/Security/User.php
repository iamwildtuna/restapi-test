<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use stdClass;

class User implements UserInterface
{
    private int $id;
    private string $username;
    private string $session;
    private string $password = '';
    private array $rights = [];
    private array $roles = [];
    private string $type;

    public function __construct(stdClass $payload)
    {
        $this->id = (int) $payload->sub;
        $this->username = $payload->fio;
        $this->session = $payload->jti;
        $this->type = $payload->user_type;
        $this->rights = $payload->rights;
    }

    public function checkRight($right): bool
    {
        return in_array($right, $this->rights, true);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): string
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return '';
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /* Setters */
    public function setSession(string $session): self
    {
        $this->session = $session;

        return $this;
    }


    /* Getters */
    public function getSession(): string
    {
        return $this->session;
    }

    /**
     * @return array
     */
    public function getRights(): array
    {
        return $this->rights;
    }

    /**
     * @param  array  $rights
     */
    public function setRights(array $rights): void
    {
        $this->rights = $rights;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
