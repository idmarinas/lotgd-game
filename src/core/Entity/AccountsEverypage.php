<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccountsEverypage.
 *
 * @ORM\Table(name="accounts_everypage")
 * @ORM\Entity
 */
class AccountsEverypage
{
    /**
     * @var int
     *
     * @ORM\Column(name="acctid", type="integer", nullable=false, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $acctid;

    /**
     * @var string
     *
     * @ORM\Column(name="allowednavs", type="text", length=16777215, nullable=true)
     */
    private $allowednavs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="laston", type="datetime", nullable=false)
     */
    private $laston;

    /**
     * @var float
     *
     * @ORM\Column(name="gentime", type="float", precision=10, scale=0, nullable=true)
     */
    private $gentime;

    /**
     * @var int
     *
     * @ORM\Column(name="gentimecount", type="integer", nullable=true, options={"unsigned":true})
     */
    private $gentimecount;

    /**
     * @var int
     *
     * @ORM\Column(name="gensize", type="integer", nullable=true, options={"unsigned":true})
     */
    private $gensize;

    /**
     * Set the value of Acctid.
     *
     * @param int acctid
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
     *
     * @return int
     */
    public function getAcctid(): int
    {
        return $this->acctid;
    }

    /**
     * Set the value of Allowednavs.
     *
     * @param string allowednavs
     *
     * @return self
     */
    public function setAllowednavs($allowednavs)
    {
        $this->allowednavs = $allowednavs;

        return $this;
    }

    /**
     * Get the value of Allowednavs.
     *
     * @return string
     */
    public function getAllowednavs(): string
    {
        return $this->allowednavs;
    }

    /**
     * Set the value of Laston.
     *
     * @param \DateTime laston
     *
     * @return self
     */
    public function setLaston(\DateTime $laston)
    {
        $this->laston = $laston;

        return $this;
    }

    /**
     * Get the value of Laston.
     *
     * @return \DateTime
     */
    public function getLaston(): \DateTime
    {
        return $this->laston;
    }

    /**
     * Set the value of Gentime.
     *
     * @param float gentime
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
     *
     * @return float
     */
    public function getGentime(): float
    {
        return $this->gentime;
    }

    /**
     * Set the value of Gentimecount.
     *
     * @param int gentimecount
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
     *
     * @return int
     */
    public function getGentimecount(): int
    {
        return $this->gentimecount;
    }

    /**
     * Set the value of Gensize.
     *
     * @param int gensize
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
     *
     * @return int
     */
    public function getGensize(): int
    {
        return $this->gensize;
    }
}
