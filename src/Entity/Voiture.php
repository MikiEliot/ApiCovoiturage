<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $immatriculation = null;

    #[ORM\Column(length: 30)]
    private ?string $modele = null;

    #[ORM\Column]
    private ?int $places = null;

    #[ORM\ManyToOne]
    private ?Marque $associer = null;
    #[ORM\OneToOne(targetEntity: Eleve::class,mappedBy: 'voiture')]
    private ?Eleve $eleve = null;




    public function getId(): ?int
    {
        return $this->id;
    }


    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): static
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

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

    public function getAssocier(): ?Marque
    {
        return $this->associer;
    }

    public function setAssocier(Marque $associer): static
    {
        $this->associer = $associer;

        return $this;
    }
    public function getEleve(): ?Eleve
    {
        return $this->eleve;
    }

    public function setEleve(Eleve $eleve): static
    {
        // set the owning side of the relation if necessary
        if ($eleve->getVoiture() !== $this) {
            $eleve->setVoiture($this);
        }

        $this->eleve = $eleve;

        return $this;
    }




}
