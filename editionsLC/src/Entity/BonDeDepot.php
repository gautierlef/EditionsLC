<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BonDeDepotRepository")
 */
class BonDeDepot
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Livre", inversedBy="bonDeDepots")
     */
    private $livre;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbEnvoyes;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbVendus;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $destinataire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLivre(): ?Livre
    {
        return $this->livre;
    }

    public function setLivre(?Livre $livre): self
    {
        $this->livre = $livre;

        return $this;
    }

    public function getNbEnvoyes(): ?int
    {
        return $this->nbEnvoyes;
    }

    public function setNbEnvoyes(int $nbEnvoyes): self
    {
        $this->nbEnvoyes = $nbEnvoyes;

        return $this;
    }

    public function getNbVendus(): ?int
    {
        return $this->nbVendus;
    }

    public function setNbVendus(int $nbVendus): self
    {
        $this->nbVendus = $nbVendus;

        return $this;
    }

    public function getDestinataire(): ?string
    {
        return $this->destinataire;
    }

    public function setDestinataire(string $destinataire): self
    {
        $this->destinataire = $destinataire;

        return $this;
    }
}
