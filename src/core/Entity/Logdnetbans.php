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
 * Logdnetbans.
 *
 * @ORM\Table(name="logdnetbans")
 * @ORM\Entity
 */
class Logdnetbans
{
    /**
     * @var int
     *
     * @ORM\Column(name="banid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $banid;

    /**
     * @var string
     *
     * @ORM\Column(name="bantype", type="string", length=20, nullable=false)
     */
    private $bantype;

    /**
     * @var string
     *
     * @ORM\Column(name="banvalue", type="string", length=255, nullable=false)
     */
    private $banvalue;

    /**
     * Set the value of Banid.
     *
     * @param int $banid
     *
     * @return self
     */
    public function setBanid($banid)
    {
        $this->banid = $banid;

        return $this;
    }

    /**
     * Get the value of Banid.
     */
    public function getBanid(): int
    {
        return $this->banid;
    }

    /**
     * Set the value of Bantype.
     *
     * @param string $bantype
     *
     * @return self
     */
    public function setBantype($bantype)
    {
        $this->bantype = $bantype;

        return $this;
    }

    /**
     * Get the value of Bantype.
     */
    public function getBantype(): string
    {
        return $this->bantype;
    }

    /**
     * Set the value of Banvalue.
     *
     * @param string $banvalue
     *
     * @return self
     */
    public function setBanvalue($banvalue)
    {
        $this->banvalue = $banvalue;

        return $this;
    }

    /**
     * Get the value of Banvalue.
     */
    public function getBanvalue(): string
    {
        return $this->banvalue;
    }
}
