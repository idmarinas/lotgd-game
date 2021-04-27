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

namespace Lotgd\Bundle\AdminBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * The admin.panel.donation.manual.add event is dispatched each time a manual donation is added.
 */
class DonationManualEvent extends Event
{
    /**
     * Event that occurs before the manual donation is saved..
     */
    public const PRE = 'admin.panel.donation.manual.add.pre';

    /**
     * Event that occurs after the manual donation is saved.
     */
    public const POST = 'admin.panel.donation.manual.add.post';

    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get points to add.
     */
    public function getPoints(): int
    {
        return $this->data['points'];
    }

    /**
     * Set new points to add.
     */
    public function setPoints(int $points): self
    {
        $this->data['points'] = $points;

        return $this;
    }

    /**
     * Get reason of manual donation.
     */
    public function getReason(): string
    {
        return $this->data['reason'];
    }

    /**
     * Set (change) reason of manual donation.
     */
    public function setReason(string $reason): self
    {
        $this->data['reason'] = $reason;

        return $this;
    }
}
