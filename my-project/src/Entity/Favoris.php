<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FavorisRepository")
 */
class Favoris
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Application\Sonata\UserBundle\Entity\User", inversedBy="favoris")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Films")
     * @ORM\JoinColumn(nullable=false)
     */
    private $film;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?User
    {
        return $this->user;
    }

    public function setUtilisateur(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFilm(): ?Films
    {
        return $this->film;
    }

    public function setFilm(?Films $film): self
    {
        $this->film = $film;

        return $this;
    }
}
