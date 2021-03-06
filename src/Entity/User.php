<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    const STARTING_CREDITS = 5000;
    const REDEEMABLE_CREDIT_AMOUNT = 1000;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="integer")
     */
    private $credits;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bet", mappedBy="user", orphanRemoval=true)
     */
    private $bets;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $displayName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creditRedemptionDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShopItem", inversedBy="users")
     */
    private $flair;

    /**
     * @ORM\ManyToOne(targetEntity="Badge", inversedBy="user")
     */
    private $badge;

    public function __construct()
    {
        $this->creditRedemptionDate = new \DateTime();
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
     * @return null|string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCredits(): ?int
    {
        return $this->credits;
    }

    /**
     * @param int $credits
     * @return User
     */
    public function setCredits(int $credits): self
    {
        $this->credits = $credits;

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
     * @return User
     */
    public function addBet(Bet $bet): self
    {
        if (!$this->bets->contains($bet)) {
            $this->bets[] = $bet;
            $bet->setUser($this);
        }

        return $this;
    }

    /**
     * @param Bet $bet
     * @return User
     */
    public function removeBet(Bet $bet): self
    {
        if ($this->bets->contains($bet)) {
            $this->bets->removeElement($bet);
            // set the owning side to null (unless already changed)
            if ($bet->getUser() === $this) {
                $bet->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return User
     */
    public function setDisplayName(string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getCreditRedemptionDate(): ?\DateTimeInterface
    {
        return $this->creditRedemptionDate;
    }

    public function setCreditRedemptionDate(\DateTimeInterface $creditRedemptionDate): self
    {
        $this->creditRedemptionDate = $creditRedemptionDate;

        return $this;
    }

    public function getFlair(): ?ShopItem
    {
        return $this->flair;
    }

    public function setFlair(?ShopItem $flair): self
    {
        $this->flair = $flair;

        return $this;
    }

    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    public function setBadge(?Badge $badge): self
    {
        $this->badge = $badge;

        return $this;
    }
}
