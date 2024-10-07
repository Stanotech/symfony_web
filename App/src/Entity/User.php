<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;
    
    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'string')]
    private string $firstName;

    #[ORM\Column(type: 'string')]
    private string $lastName;

    #[ORM\ManyToOne(targetEntity: UserRole::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private UserRole $role;

    public function __construct()
    {
        $this->id = Uuid::uuid6(); // lub Uuid::uuid4() w zależności od wymagań
    }

    // Gettery i settery
}
