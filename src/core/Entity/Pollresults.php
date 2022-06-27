<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pollresults.
 *
 * @ORM\Table(name="pollresults",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="vote", columns={"account", "motditem"})
 *     }
 * )
 * @ORM\Entity
 */
class Pollresults
{
    /**
     *
     * @ORM\Column(name="resultid", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $resultid = null;

    /**
     *
     * @ORM\Column(name="choice", type="integer", options={"unsigned"=true})
     */
    private ?int $choice = 0;

    /**
     *
     * @ORM\Column(name="account", type="integer", options={"unsigned"=true})
     */
    private ?int $account = 0;

    /**
     *
     * @ORM\Column(name="motditem", type="integer", options={"unsigned"=true})
     */
    private ?int $motditem = 0;

    /**
     * Set the value of Resultid.
     *
     * @param int $resultid
     *
     * @return self
     */
    public function setResultid($resultid)
    {
        $this->resultid = $resultid;

        return $this;
    }

    /**
     * Get the value of Resultid.
     */
    public function getResultid(): int
    {
        return $this->resultid;
    }

    /**
     * Set the value of Choice.
     *
     * @param int $choice
     *
     * @return self
     */
    public function setChoice($choice)
    {
        $this->choice = $choice;

        return $this;
    }

    /**
     * Get the value of Choice.
     */
    public function getChoice(): int
    {
        return $this->choice;
    }

    /**
     * Set the value of Account.
     *
     * @param int $account
     *
     * @return self
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get the value of Account.
     */
    public function getAccount(): int
    {
        return $this->account;
    }

    /**
     * Set the value of Motditem.
     *
     * @param int $motditem
     *
     * @return self
     */
    public function setMotditem($motditem)
    {
        $this->motditem = $motditem;

        return $this;
    }

    /**
     * Get the value of Motditem.
     */
    public function getMotditem(): int
    {
        return $this->motditem;
    }
}
