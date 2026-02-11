<?php

namespace App\Entity;

use App\Repository\MeterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MeterRepository::class)]
class Meter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $serialNumber = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $location = null;

    /**
     * @var Collection<int, Reading>
     */
    #[ORM\OneToMany(targetEntity: Reading::class, mappedBy: 'meter', orphanRemoval: true)]
    #[Ignore]
    private Collection $readings;

    public function __construct()
    {
        $this->readings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(string $serialNumber): static
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, Reading>
     */
    public function getReadings(): Collection
    {
        return $this->readings;
    }

    public function addReading(Reading $reading): static
    {
        if (!$this->readings->contains($reading)) {
            $this->readings->add($reading);
            $reading->setMeter($this);
        }

        return $this;
    }

    public function removeReading(Reading $reading): static
    {
        if ($this->readings->removeElement($reading)) {
            // set the owning side to null (unless already changed)
            if ($reading->getMeter() === $this) {
                $reading->setMeter(null);
            }
        }

        return $this;
    }
}
