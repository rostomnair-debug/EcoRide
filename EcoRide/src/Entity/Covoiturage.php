<?php

namespace App\Entity;

use App\Repository\CovoiturageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CovoiturageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Covoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDepart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heureDepart = null;

    #[ORM\Column(length: 50)]
    private ?string $lieuDepart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateArrivee = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heureArrivee = null;

    #[ORM\Column(length: 50)]
    private ?string $lieuArrivee = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?int $nbPlace = null;

    #[ORM\Column]
    private ?float $prixPersonne = null;

    #[ORM\Column(length: 150, unique: true)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'covoiturages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Voiture $voiture = null;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class, mappedBy: 'covoiturages')]
    private Collection $participants;

    #[ORM\OneToMany(mappedBy: 'covoiturage', targetEntity: Avis::class)]
    private Collection $avis;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pointRdv = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pointArrivee = null;

    #[ORM\Column(type: Types::FLOAT, options: ['default' => 2])]
    private float $commissionPlateforme = 2.0;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $conducteurNom = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $conducteurPrenom = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $conducteurPseudo = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $signale = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $motifSignalement = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $fumeur = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $animaux = false;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $bagageType = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->dateDepart;
    }

    public function setDateDepart(\DateTimeInterface $dateDepart): static
    {
        $this->dateDepart = $dateDepart;

        return $this;
    }

    public function getHeureDepart(): ?\DateTimeInterface
    {
        return $this->heureDepart;
    }

    public function setHeureDepart(\DateTimeInterface $heureDepart): static
    {
        $this->heureDepart = $heureDepart;

        return $this;
    }

    public function getLieuDepart(): ?string
    {
        return $this->lieuDepart;
    }

    public function setLieuDepart(string $lieuDepart): static
    {
        $this->lieuDepart = $lieuDepart;

        return $this;
    }

    public function getDateArrivee(): ?\DateTimeInterface
    {
        return $this->dateArrivee;
    }

    public function setDateArrivee(\DateTimeInterface $dateArrivee): static
    {
        $this->dateArrivee = $dateArrivee;

        return $this;
    }

    public function getHeureArrivee(): ?\DateTimeInterface
    {
        return $this->heureArrivee;
    }

    public function setHeureArrivee(\DateTimeInterface $heureArrivee): static
    {
        $this->heureArrivee = $heureArrivee;

        return $this;
    }

    public function getLieuArrivee(): ?string
    {
        return $this->lieuArrivee;
    }

    public function setLieuArrivee(string $lieuArrivee): static
    {
        $this->lieuArrivee = $lieuArrivee;

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

    public function getNbPlace(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): static
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    public function getPrixPersonne(): ?float
    {
        return $this->prixPersonne;
    }

    public function setPrixPersonne(float $prixPersonne): static
    {
        $this->prixPersonne = $prixPersonne;

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

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(?Voiture $voiture): static
    {
        $this->voiture = $voiture;

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Utilisateur $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addCovoiturage($this);
        }

        return $this;
    }

    public function removeParticipant(Utilisateur $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeCovoiturage($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setCovoiturage($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            if ($avi->getCovoiturage() === $this) {
                $avi->setCovoiturage(null);
            }
        }

        return $this;
    }

    public function getPointRdv(): ?string
    {
        return $this->pointRdv;
    }

    public function setPointRdv(?string $pointRdv): static
    {
        $this->pointRdv = $pointRdv;

        return $this;
    }

    public function getPointArrivee(): ?string
    {
        return $this->pointArrivee;
    }

    public function setPointArrivee(?string $pointArrivee): static
    {
        $this->pointArrivee = $pointArrivee;

        return $this;
    }

    public function getCommissionPlateforme(): float
    {
        return $this->commissionPlateforme;
    }

    public function setCommissionPlateforme(float $commissionPlateforme): static
    {
        $this->commissionPlateforme = $commissionPlateforme;

        return $this;
    }

    public function getConducteurNom(): ?string
    {
        return $this->conducteurNom;
    }

    public function setConducteurNom(?string $conducteurNom): static
    {
        $this->conducteurNom = $conducteurNom;

        return $this;
    }

    public function getConducteurPrenom(): ?string
    {
        return $this->conducteurPrenom;
    }

    public function setConducteurPrenom(?string $conducteurPrenom): static
    {
        $this->conducteurPrenom = $conducteurPrenom;

        return $this;
    }

    public function getConducteurPseudo(): ?string
    {
        return $this->conducteurPseudo;
    }

    public function setConducteurPseudo(?string $conducteurPseudo): static
    {
        $this->conducteurPseudo = $conducteurPseudo;

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

    public function isFumeur(): bool
    {
        return $this->fumeur;
    }

    public function setFumeur(bool $fumeur): static
    {
        $this->fumeur = $fumeur;

        return $this;
    }

    public function isAnimaux(): bool
    {
        return $this->animaux;
    }

    public function setAnimaux(bool $animaux): static
    {
        $this->animaux = $animaux;

        return $this;
    }

    public function getBagageType(): ?string
    {
        return $this->bagageType;
    }

    public function setBagageType(?string $bagageType): static
    {
        $this->bagageType = $bagageType;

        return $this;
    }
}
