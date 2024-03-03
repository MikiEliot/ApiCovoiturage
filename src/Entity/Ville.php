<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 30)]
    private ?string $cp = null;

    #[ORM\ManyToMany(targetEntity: Eleve::class)]
    private Collection $habitant;

    #[ORM\OneToMany(targetEntity: Trajet::class, mappedBy: 'depart')]
    private Collection $depart;

    #[ORM\OneToMany(targetEntity: Trajet::class,mappedBy: 'arrivee')]
    private Collection $arrivee;

    public function __construct()
    {
        $this->habiter = new ArrayCollection();
        $this->depart = new ArrayCollection();
        $this->arrivee = new ArrayCollection();
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

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): static
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * @return Collection<int, Eleve>
     */
    public function getHabitant(): Collection
    {
        return $this->habitant;
    }

    public function addHabitant(Eleve $habitant): static
    {
        if (!$this->habitant->contains($habitant)) {
            $this->habitant->add($habitant);
        }

        return $this;
    }

    public function removeHabitant(Eleve $habitant): static
    {
        $this->habitant->removeElement($habitant);

        return $this;
    }

    /**
     * @return Collection<int, Trajet>
     */
    public function getDepart(): Collection
    {
        return $this->depart;
    }

    public function addDepart(Trajet $depart): static
    {
        if (!$this->depart->contains($depart)) {
            $this->depart->add($depart);
        }

        return $this;
    }

    public function removeDepart(Trajet $depart): static
    {
        $this->depart->removeElement($depart);

        return $this;
    }

    /**
     * @return Collection<int, Trajet>
     */
    public function getArrivee(): Collection
    {
        return $this->arrivee;
    }

    public function addArrivee(Trajet $arrivee): static
    {
        if (!$this->arrivee->contains($arrivee)) {
            $this->arrivee->add($arrivee);
        }

        return $this;
    }

    public function removeArrivee(Trajet $arrivee): static
    {
        $this->arrivee->removeElement($arrivee);

        return $this;
    }
}
