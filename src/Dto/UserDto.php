<?php

namespace App\Dto;

use App\Entity\User;
use App\Entity\Role;

class UserDto
{
    public int $id;
    public string $email;
    public ?string $username;
    public ?Role $role;
    public string $first_name;
    public string $last_name;
    public \DateTimeInterface $created_at;
    public bool $is_verify;

    // Constructeur qui remplit les données à partir de l'entité User
    public function __construct(User $user)
    {
        $this->id = $user->getId();
        $this->email = $user->getEmail();
        $this->role = $user->getRole();
        $this->first_name = $user->getFirstName();
        $this->last_name = $user->getLastName();
        $this->username = $user->getUsername();
        $this->created_at = $user->getCreatedAt();
        $this->is_verify = $user->isVerify();

    }
}
