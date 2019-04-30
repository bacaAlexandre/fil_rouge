<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateursRepository")
 */
class User extends BaseUser
{
        public function __construct()
    {
        parent::__construct();
        $this->commentaires = new ArrayCollection();
        $this->favoris = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

//    /**
//     * @ORM\Column(type="string", length=255)
//     */
//    protected $pseudo;
//
//    /**
//     * @ORM\Column(type="string", length=255)
//     */
//    protected $email;
//
//    /**
//     * @ORM\Column(type="string", length=255)
//     */
//    protected $mdp;
//
//    /**
//     * @ORM\OneToMany(targetEntity="App\Entity\Commentaires", mappedBy="utilisateur", orphanRemoval=true)
//     */
//    protected $commentaires;
//
//    /**
//     * @ORM\Column(type="boolean")
//     */
//    protected $isActif;
//
//    /**
//     * @ORM\OneToMany(targetEntity="App\Entity\Favoris", mappedBy="utilisateur", orphanRemoval=true)
//     */
//    protected $favoris;
//
//    /**
//     * @ORM\OneToMany(targetEntity="App\Entity\Notes", mappedBy="utilisateur")
//     */
//    protected $notes;
//
//
//
//    public function getId(): ?int
//    {
//        return $this->id;
//    }
//
//    public function getPseudo(): ?string
//    {
//        return $this->pseudo;
//    }
//
//    public function setPseudo(string $pseudo): self
//    {
//        $this->pseudo = $pseudo;
//
//        return $this;
//    }
//
//    public function getEmail(): ?string
//    {
//        return $this->email;
//    }
//
//    public function setEmail(string $email): self
//    {
//        $this->email = $email;
//
//        return $this;
//    }
//
//    public function getMdp(): ?string
//    {
//        return $this->mdp;
//    }
//
//    public function setMdp(string $mdp): self
//    {
//        $this->mdp = $mdp;
//
//        return $this;
//    }
//
//    /**
//     * @return Collection|Commentaires[]
//     */
//    public function getCommentaires(): Collection
//    {
//        return $this->commentaires;
//    }
//
//    public function addCommentaire(Commentaires $commentaire): self
//    {
//        if (!$this->commentaires->contains($commentaire)) {
//            $this->commentaires[] = $commentaire;
//            $commentaire->setUtilisateur($this);
//        }
//
//        return $this;
//    }
//
//    public function removeCommentaire(Commentaires $commentaire): self
//    {
//        if ($this->commentaires->contains($commentaire)) {
//            $this->commentaires->removeElement($commentaire);
//            // set the owning side to null (unless already changed)
//            if ($commentaire->getUtilisateur() === $this) {
//                $commentaire->setUtilisateur(null);
//            }
//        }
//
//        return $this;
//    }
//
//    public function getIsActif(): ?bool
//    {
//        return $this->isActif;
//    }
//
//    public function setIsActif(bool $isActif): self
//    {
//        $this->isActif = $isActif;
//
//        return $this;
//    }
//
//    /**
//     * @return Collection|Favoris[]
//     */
//    public function getFavoris(): Collection
//    {
//        return $this->favoris;
//    }
//
//    public function addFavori(Favoris $favori): self
//    {
//        if (!$this->favoris->contains($favori)) {
//            $this->favoris[] = $favori;
//            $favori->setUtilisateur($this);
//        }
//
//        return $this;
//    }
//
//    public function removeFavori(Favoris $favori): self
//    {
//        if ($this->favoris->contains($favori)) {
//            $this->favoris->removeElement($favori);
//            // set the owning side to null (unless already changed)
//            if ($favori->getUtilisateur() === $this) {
//                $favori->setUtilisateur(null);
//            }
//        }
//
//        return $this;
//    }
//
//    /**
//     * @return Collection|Notes[]
//     */
//    public function getNotes(): Collection
//    {
//        return $this->notes;
//    }
//
//    public function addNote(Notes $note): self
//    {
//        if (!$this->notes->contains($note)) {
//            $this->notes[] = $note;
//            $note->setUtilisateur($this);
//        }
//
//        return $this;
//    }
//
//    public function removeNote(Notes $note): self
//    {
//        if ($this->notes->contains($note)) {
//            $this->notes->removeElement($note);
//            // set the owning side to null (unless already changed)
//            if ($note->getUtilisateur() === $this) {
//                $note->setUtilisateur(null);
//            }
//        }
//
//        return $this;
//    }
}
