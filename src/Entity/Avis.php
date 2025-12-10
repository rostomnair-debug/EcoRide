<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commentaire = null;

    #[ORM\Column(length: 50)]
    private ?string $note = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'avisDeposes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    private ?Covoiturage $covoiturage = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $auteurNom = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $auteurPrenom = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $auteurPseudo = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $signale = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $motifSignalement = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'avisRecus')]
    private ?Utilisateur $ratedUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getCovoiturage(): ?Covoiturage
    {
        return $this->covoiturage;
    }

    public function setCovoiturage(?Covoiturage $covoiturage): static
    {
        $this->covoiturage = $covoiturage;

        return $this;
    }

    public function getAuteurNom(): ?string
    {
        return $this->auteurNom;
    }

    public function setAuteurNom(?string $auteurNom): static
    {
        $this->auteurNom = $auteurNom;

        return $this;
    }

    public function getAuteurPrenom(): ?string
    {
        return $this->auteurPrenom;
    }

    public function setAuteurPrenom(?string $auteurPrenom): static
    {
        $this->auteurPrenom = $auteurPrenom;

        return $this;
    }

    public function getAuteurPseudo(): ?string
    {
        return $this->auteurPseudo;
    }

    public function setAuteurPseudo(?string $auteurPseudo): static
    {
        $this->auteurPseudo = $auteurPseudo;

        return $this;
    }

    public function isSignale(): bool
    {
        return $this->signale;
    }

    public function setSignale(bool $signale): static
    {
        $this->signale = $signale;

        return $this;
    }

    public function getMotifSignalement(): ?string
    {
        return $this->motifSignalement;
    }

    public function setMotifSignalement(?string $motifSignalement): static
    {
        $this->motifSignalement = $motifSignalement;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    public function getRatedUser(): ?Utilisateur
    {
        return $this->ratedUser;
    }

    public function setRatedUser(?Utilisateur $ratedUser): static
    {
        $this->ratedUser = $ratedUser;

        return $this;
    }
}
