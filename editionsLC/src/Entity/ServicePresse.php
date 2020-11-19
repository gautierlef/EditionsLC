<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServicePresseRepository")
 */
class ServicePresse
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Livre", inversedBy="servicePresses")
     */
    private $livre;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbDonnes;

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

    public function getNbDonnes(): ?int
    {
        return $this->nbDonnes;
    }

    public function setNbDonnes(int $nbDonnes): self
    {
        $this->nbDonnes = $nbDonnes;

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
