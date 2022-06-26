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
 * Structure of table "accounts_everypage" in data base.
 *
 * @ORM\Table(name="accounts_everypage")
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\AccountsEverypageRepository")
 */
class AccountsEverypage
{
    /**
     *
     * @ORM\Column(name="acctid", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private ?int $acctid = null;

    /**
     * @var float|null
     *
     * @ORM\Column(name="gentime", type="float", precision=10, options={"unsigned"=true})
     */
    private ?float $gentime = 0;

    /**
     *
     * @ORM\Column(name="gentimecount", type="integer", options={"unsigned"=true})
     */
    private ?int $gentimecount = 0;

    /**
     *
     * @ORM\Column(name="gensize", type="integer", options={"unsigned"=true})
     */
    private ?int $gensize = 0;

    /**
     * Set the value of Acctid.
     *
     * @param int $acctid
     *
     * @return self
     */
    public function setAcctid($acctid)
    {
        $this->acctid = $acctid;

        return $this;
    }

    /**
     * Get the value of Acctid.
     */
    public function getAcctid(): int
    {
        return $this->acctid;
    }

    /**
     * Set the value of Gentime.
     *
     * @param float $gentime
     *
     * @return self
     */
    public function setGentime($gentime)
    {
        $this->gentime = $gentime;

        return $this;
    }

    /**
     * Get the value of Gentime.
     */
    public function getGentime(): float
    {
        return $this->gentime;
    }

    /**
     * Set the value of Gentimecount.
     *
     * @param int $gentimecount
     *
     * @return self
     */
    public function setGentimecount($gentimecount)
    {
        $this->gentimecount = $gentimecount;

        return $this;
    }

    /**
     * Get the value of Gentimecount.
     */
    public function getGentimecount(): int
    {
        return $this->gentimecount;
    }

    /**
     * Set the value of Gensize.
     *
     * @param int $gensize
     *
     * @return self
     */
    public function setGensize($gensize)
    {
        $this->gensize = $gensize;

        return $this;
    }

    /**
     * Get the value of Gensize.
     */
    public function getGensize(): int
    {
        return $this->gensize;
    }
}
