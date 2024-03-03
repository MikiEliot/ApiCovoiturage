<?php

namespace App\Entity;

use App\Repository\TrajetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrajetRepository::class)]
class Trajet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column]
    private ?float $distance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_trajet = null;

    #[ORM\Column]
    private ?int $places = null;

    #[ORM\ManyToOne(targetEntity: Eleve::class)]
    #[ORM\JoinColumn(name: "id_conducteur", referencedColumnName: "id",nullable: false)]
    private ?Eleve $conducteur = null;

    #[ORM\ManyToMany(targetEntity: Eleve::class, mappedBy: 'participations')]
    private Collection $participants;


    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'depart')]
    #[ORM\JoinColumn(name: "id_ville_arrivee", referencedColumnName: "id",nullable: false)]
    private ?Ville $ville_depart = null;

    #[ORM\ManyToOne(targetEntity: Ville::class, inversedBy: 'arrivee')]
    #[ORM\JoinColumn(name: "id_ville_depart", referencedColumnName: "id",nullable: false)]
    private ?Ville $ville_arrivee = null;


    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): static
    {
        $this->distance = $distance;

        return $this;
    }

    public function getDateTrajet(): ?\DateTimeInterface
    {
        return $this->date_trajet;
    }

    public function setDateTrajet(\DateTimeInterface $date_trajet): static
    {
        $this->date_trajet = $date_trajet;

        return $this;
    }

    public function getPlaces(): ?int
    {
        return $this->places;
    }

    public function setPlaces(int $places): static
    {
        $this->places = $places;

        return $this;
    }

    public function getConducteur(): ?Eleve
    {
        return $this->conducteur;
    }

    public function setConducteur(?Eleve $conducteur): static
    {
        $this->conducteur = $conducteur;

        return $this;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function setParticipants(Collection $participants): void
    {
        $this->participants = $participants;
    }

    public function addParticipant(Eleve $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }


    public function getVilleDepart(): ?Ville
    {
        return $this->ville_depart;
    }

    public function setVilleDepart(Ville $ville_depart): static
    {
        $this->ville_depart = $ville_depart;

        return $this;
    }

    public function getVilleArrivee(): ?Ville
    {
        return $this->ville_arrivee;
    }

    public function setVilleArrivee(Ville $ville_arrivee): static
    {
        $this->ville_arrivee = $ville_arrivee;

        return $this;
    }

    public function getConduire(): ?Eleve
    {
        return $this->conduire;
    }

    public function setConduire(Eleve $conduire): static
    {
        $this->conduire = $conduire;

        return $this;
    }


}
