<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */
namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccountsOutput.
 *
 * @ORM\Table(name="accounts_output")
 * @ORM\Entity
 */
class AccountsOutput
{
    /**
     * @var int
     *
     * @ORM\Column(name="acctid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $acctid;

    /**
     * @var string
     *
     * @ORM\Column(name="output", type="blob", length=16777215, nullable=false)
     */
    private $output;

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
     * Set the value of Output.
     *
     * @param string output
     *
     * @return self
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Get the value of Output.
     *
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }
}
