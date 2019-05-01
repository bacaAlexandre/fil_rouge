<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotesRepository")
 */
class Notes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $valeur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Application\Sonata\UserBundle\Entity\User", inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\films", inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $film;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValeur(): ?int
    {
        return $this->valeur;
    }

    public function setValeur(int $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
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

    public function getFilm(): ?films
    {
        return $this->film;
    }

    public function setFilm(?films $film): self
    {
        $this->film = $film;

        return $this;
    }
}
