<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Entity\User;

trait Donation
{
    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    private $donation = 0;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    private $donationSpent = 0;

    public function getDonation(): ?int
    {
        return $this->donation;
    }

    public function setDonation(int $donation): self
    {
        $this->donation = $donation;

        return $this;
    }

    public function getDonationSpent(): ?int
    {
        return $this->donationSpent;
    }

    public function setDonationSpent(int $donationSpent): self
    {
        $this->donationSpent = $donationSpent;

        return $this;
    }
}
