<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OutcomeRepository")
 */
class Outcome
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $payout;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $won;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bet", mappedBy="outcome", orphanRemoval=true)
     */
    private $bets;

    public function __construct()
    {
        $this->bets = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPayout()
    {
        return $this->payout;
    }

    /**
     * @param $payout
     * @return Outcome
     */
    public function setPayout($payout): self
    {
        $this->payout = $payout;

        return $this;
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
     * @return Outcome
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getWon(): ?bool
    {
        return $this->won;
    }

    /**
     * @param bool $won
     * @return Outcome
     */
    public function setWon(bool $won): self
    {
        $this->won = $won;

        return $this;
    }

    /**
     * @return Collection|Bet[]
     */
    public function getBets(): Collection
    {
        return $this->bets;
    }

    /**
     * @param Bet $bet
     * @return Outcome
     */
    public function addBet(Bet $bet): self
    {
        if (!$this->bets->contains($bet)) {
            $this->bets[] = $bet;
            $bet->setOutcome($this);
        }

        return $this;
    }

    /**
     * @param Bet $bet
     * @return Outcome
     */
    public function removeBet(Bet $bet): self
    {
        if ($this->bets->contains($bet)) {
            $this->bets->removeElement($bet);
            // set the owning side to null (unless already changed)
            if ($bet->getOutcome() === $this) {
                $bet->setOutcome(null);
            }
        }

        return $this;
    }
}
