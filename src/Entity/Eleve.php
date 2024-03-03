<?php

namespace App\Entity;

use App\Repository\EleveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EleveRepository::class)]
class Eleve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $telephone = null;

    #[ORM\Column(length: 50)]
    private ?string $email = null;

    #[ORM\OneToOne(inversedBy: 'eleve', cascade: ['persist', 'remove'])]
    private ?Voiture $voiture = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $habiter = null;

    #[ORM\OneToMany(targetEntity: Trajet::class, mappedBy: 'conducteur')]
    private Collection $conduire;

    #[ORM\OneToOne(targetEntity: Compte::class, inversedBy: 'eleve')]
    #[ORM\JoinColumn(name: 'lier_id', referencedColumnName: 'id', nullable: false)]
    private ?Compte $lier = null;

//    #[ORM\OneToOne(mappedBy: 'conduire', cascade: ['persist', 'remove'])]
//    private ?Trajet $trajet_conducteur = null;

    #[ORM\ManyToMany(targetEntity: Trajet::class)]
    private Collection $participations;


    public function __construct()
    {
        $this->conduire = new ArrayCollection();
        $this->participer = new ArrayCollection();
        $this->participations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

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

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(?Voiture $voiture): static
    {
        $this->voiture = $voiture;

        return $this;
    }

    public function getHabiter(): ?Ville
    {
        return $this->habiter;
    }

    public function setHabiter(Ville $habiter): static
    {
        $this->habiter = $habiter;

        return $this;
    }

    /**
     * @return Collection<int, Trajet>
     */
    public function getConduire(): Collection
    {
        return $this->conduire;
    }

    public function addConduire(Trajet $conduire): static
    {
        if (!$this->conduire->contains($conduire)) {
            $this->conduire->add($conduire);
            $conduire->setConducteur($this);
        }

        return $this;
    }

    public function removeConduire(Trajet $conduire): static
    {
        if ($this->conduire->removeElement($conduire)) {
            // set the owning side to null (unless already changed)
            if ($conduire->getConducteur() === $this) {
                $conduire->setConducteur(null);
            }
        }

        return $this;
    }


    public function getLier(): ?Compte
    {
        return $this->lier;
    }

    public function setLier(Compte $lier): static
    {
        $this->lier = $lier;

        return $this;
    }

    public function getTrajetConducteur(): ?Trajet
    {
        return $this->trajet_conducteur;
    }

    public function setTrajetConducteur(Trajet $trajet_conducteur): static
    {
        // set the owning side of the relation if necessary
        if ($trajet_conducteur->getConduire() !== $this) {
            $trajet_conducteur->setConduire($this);
        }

        $this->trajet_conducteur = $trajet_conducteur;

        return $this;
    }

    /**
     * @return Collection<int, Trajet>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Trajet $participation): static
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->addParticiper($this);
        }

        return $this;
    }

    public function removeParticipation(Trajet $participation): static
    {
        if ($this->participations->removeElement($participation)) {
            $participation->removeParticiper($this);
        }

        return $this;
    }

}
