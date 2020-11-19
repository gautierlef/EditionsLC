<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LivreRepository")
 */
class Livre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Auteur", inversedBy="livres")
     */
    private $auteur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vente", mappedBy="livre")
     */
    private $ventes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Participation", mappedBy="livre")
     */
    private $participations;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BonDeDepot", mappedBy="livre")
     */
    private $bonDeDepots;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ServicePresse", mappedBy="livre")
     */
    private $servicePresses;

    public function __construct()
    {
        $this->ventes = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->bonDeDepots = new ArrayCollection();
        $this->servicePresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAuteur(): ?Auteur
    {
        return $this->auteur;
    }

    public function setAuteur(?Auteur $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * @return Collection|Vente[]
     */
    public function getVentes(): Collection
    {
        return $this->ventes;
    }

    public function addVente(Vente $vente): self
    {
        if (!$this->ventes->contains($vente)) {
            $this->ventes[] = $vente;
            $vente->setLivre($this);
        }

        return $this;
    }

    public function removeVente(Vente $vente): self
    {
        if ($this->ventes->contains($vente)) {
            $this->ventes->removeElement($vente);
            // set the owning side to null (unless already changed)
            if ($vente->getLivre() === $this) {
                $vente->setLivre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Participation[]
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setLivre($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->contains($participation)) {
            $this->participations->removeElement($participation);
            // set the owning side to null (unless already changed)
            if ($participation->getLivre() === $this) {
                $participation->setLivre(null);
            }
        }

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection|BonDeDepot[]
     */
    public function getBonDeDepots(): Collection
    {
        return $this->bonDeDepots;
    }

    public function addBonDeDepot(BonDeDepot $bonDeDepot): self
    {
        if (!$this->bonDeDepots->contains($bonDeDepot)) {
            $this->bonDeDepots[] = $bonDeDepot;
            $bonDeDepot->setLivre($this);
        }

        return $this;
    }

    public function removeBonDeDepot(BonDeDepot $bonDeDepot): self
    {
        if ($this->bonDeDepots->contains($bonDeDepot)) {
            $this->bonDeDepots->removeElement($bonDeDepot);
            // set the owning side to null (unless already changed)
            if ($bonDeDepot->getLivre() === $this) {
                $bonDeDepot->setLivre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ServicePresse[]
     */
    public function getServicePresses(): Collection
    {
        return $this->servicePresses;
    }

    public function addServicePress(ServicePresse $servicePress): self
    {
        if (!$this->servicePresses->contains($servicePress)) {
            $this->servicePresses[] = $servicePress;
            $servicePress->setLivre($this);
        }

        return $this;
    }

    public function removeServicePress(ServicePresse $servicePress): self
    {
        if ($this->servicePresses->contains($servicePress)) {
            $this->servicePresses->removeElement($servicePress);
            // set the owning side to null (unless already changed)
            if ($servicePress->getLivre() === $this) {
                $servicePress->setLivre(null);
            }
        }

        return $this;
    }
}
