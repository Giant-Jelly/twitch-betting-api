<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoundRepository")
 */
class Round
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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outcome", mappedBy="round")
     */
    private $outcomes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $finished = false;

    public function __construct()
    {
        $this->outcomes = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Round
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Outcome[]
     */
    public function getOutcomes(): Collection
    {
        return $this->outcomes;
    }

    /**
     * @param Outcome $outcome
     * @return Round
     */
    public function addOutcome(Outcome $outcome): self
    {
        if (!$this->outcomes->contains($outcome)) {
            $this->outcomes[] = $outcome;
            $outcome->setRound($this);
        }

        return $this;
    }

    /**
     * @param Outcome $outcome
     * @return Round
     */
    public function removeOutcome(Outcome $outcome): self
    {
        if ($this->outcomes->contains($outcome)) {
            $this->outcomes->removeElement($outcome);
            // set the owning side to null (unless already changed)
            if ($outcome->getRound() === $this) {
                $outcome->setRound(null);
            }
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getFinished(): ?bool
    {
        return $this->finished;
    }

    /**
     * @param bool $finished
     * @return Round
     */
    public function setFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }
}
