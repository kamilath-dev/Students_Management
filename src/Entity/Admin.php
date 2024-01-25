<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $adname = null;

    #[ORM\Column(length: 20)]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdname(): ?string
    {
        return $this->adname;
    }

    public function setAdname(string $adname): static
    {
        $this->adname = $adname;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
}
