<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
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
    private $donationspent = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="donationconfig", type="array")
     */
    private $donationconfig = [];

    public function getDonation(): ?int
    {
        return $this->donation;
    }

    public function setDonation(int $donation): self
    {
        $this->donation = $donation;

        return $this;
    }

    public function getDonationspent(): ?int
    {
        return $this->donationspent;
    }

    public function setDonationspent(int $donationspent): self
    {
        $this->donationspent = $donationspent;

        return $this;
    }

    public function setDonationconfig(array $donationconfig): self
    {
        $this->donationconfig = $donationconfig;

        return $this;
    }

    public function getDonationconfig()
    {
        return $this->donationconfig;
    }
}
