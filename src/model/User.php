<?php
class User
{
    private int $id;
    private string $username;
    private string $passwordHash;
    private string $role;
    private string $createdAt;

    public function __construct(string $username, string $passwordHash, string $role = 'user', ?int $id = null, ?string $createdAt = null)
    {
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
        if ($id !== null) {
            $this->id = $id;
        }
        $this->createdAt = $createdAt ?? date('Y-m-d H:i:s');
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }
}
