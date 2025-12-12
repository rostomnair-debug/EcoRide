<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 20, options: ['default' => 'user'])]
    private string $role = 'user';

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $verifie = false;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $verificationToken = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $resetTokenExpiresAt = null;

    #[ORM\Column(type: 'integer', options: ['default' => 20])]
    private int $credit = 20;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $suspended = false;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $noteMoyenne = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Avis::class, orphanRemoval: true)]
    private Collection $avisDeposes;

    #[ORM\OneToMany(mappedBy: 'ratedUser', targetEntity: Avis::class)]
    private Collection $avisRecus;

    #[ORM\OneToMany(mappedBy: 'proprietaire', targetEntity: Voiture::class)]
    private Collection $voitures;

    #[ORM\ManyToMany(targetEntity: Covoiturage::class, inversedBy: 'participants')]
    #[ORM\JoinTable(name: 'covoiturage_participant')]
    private Collection $covoiturages;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $cguAcceptedAt = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $cguVersion = null;

    public function __construct()
    {
        $this->avisDeposes = new ArrayCollection();
        $this->avisRecus = new ArrayCollection();
        $this->voitures = new ArrayCollection();
        $this->covoiturages = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNoteMoyenne(): ?float
    {
        return $this->noteMoyenne;
    }

    public function setNoteMoyenne(?float $noteMoyenne): static
    {
        $this->noteMoyenne = $noteMoyenne;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCguAcceptedAt(): ?\DateTimeInterface
    {
        return $this->cguAcceptedAt;
    }

    public function setCguAcceptedAt(?\DateTimeInterface $cguAcceptedAt): static
    {
        $this->cguAcceptedAt = $cguAcceptedAt;

        return $this;
    }

    public function getCguVersion(): ?string
    {
        return $this->cguVersion;
    }

    public function setCguVersion(?string $cguVersion): static
    {
        $this->cguVersion = $cguVersion;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $normalized = strtoupper($this->role ?: 'USER');
        $normalized = str_starts_with($normalized, 'ROLE_') ? $normalized : 'ROLE_' . $normalized;

        return [$normalized];
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function isVerifie(): bool
    {
        return $this->verifie;
    }

    public function setVerifie(bool $verifie): static
    {
        $this->verifie = $verifie;

        return $this;
    }

    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    public function setVerificationToken(?string $verificationToken): static
    {
        $this->verificationToken = $verificationToken;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getResetTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->resetTokenExpiresAt;
    }

    public function setResetTokenExpiresAt(?\DateTimeInterface $resetTokenExpiresAt): static
    {
        $this->resetTokenExpiresAt = $resetTokenExpiresAt;

        return $this;
    }

    public function getCredit(): int
    {
        return $this->credit;
    }

    public function setCredit(int $credit): static
    {
        $this->credit = $credit;

        return $this;
    }

    public function addCredit(int $amount): static
    {
        $this->credit += $amount;

        return $this;
    }

    public function removeCredit(int $amount): static
    {
        $this->credit = max(0, $this->credit - $amount);

        return $this;
    }

    public function isSuspended(): bool
    {
        return $this->suspended;
    }

    public function setSuspended(bool $suspended): static
    {
        $this->suspended = $suspended;

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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void
    {
        // Rien à effacer pour le moment.
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvisDeposes(): Collection
    {
        return $this->avisDeposes;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvisRecus(): Collection
    {
        return $this->avisRecus;
    }

    public function addAvisRecus(Avis $avisRecus): static
    {
        if (!$this->avisRecus->contains($avisRecus)) {
            $this->avisRecus->add($avisRecus);
            $avisRecus->setRatedUser($this);
        }

        return $this;
    }

    public function removeAvisRecus(Avis $avisRecus): static
    {
        if ($this->avisRecus->removeElement($avisRecus)) {
            if ($avisRecus->getRatedUser() === $this) {
                $avisRecus->setRatedUser(null);
            }
        }

        return $this;
    }

    public function addAvisDepose(Avis $avisDepose): static
    {
        if (!$this->avisDeposes->contains($avisDepose)) {
            $this->avisDeposes->add($avisDepose);
            $avisDepose->setUtilisateur($this);
        }

        return $this;
    }

    public function removeAvisDepose(Avis $avisDepose): static
    {
        if ($this->avisDeposes->removeElement($avisDepose)) {
            if ($avisDepose->getUtilisateur() === $this) {
                $avisDepose->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Voiture>
     */
    public function getVoitures(): Collection
    {
        return $this->voitures;
    }

    public function addVoiture(Voiture $voiture): static
    {
        if (!$this->voitures->contains($voiture)) {
            $this->voitures->add($voiture);
            $voiture->setProprietaire($this);
        }

        return $this;
    }

    public function removeVoiture(Voiture $voiture): static
    {
        if ($this->voitures->removeElement($voiture)) {
            if ($voiture->getProprietaire() === $this) {
                $voiture->setProprietaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Covoiturage>
     */
    public function getCovoiturages(): Collection
    {
        return $this->covoiturages;
    }

    public function addCovoiturage(Covoiturage $covoiturage): static
    {
        if (!$this->covoiturages->contains($covoiturage)) {
            $this->covoiturages->add($covoiturage);
            $covoiturage->addParticipant($this);
        }

        return $this;
    }

    public function removeCovoiturage(Covoiturage $covoiturage): static
    {
        if ($this->covoiturages->removeElement($covoiturage)) {
            $covoiturage->removeParticipant($this);
        }

        return $this;
    }
}
