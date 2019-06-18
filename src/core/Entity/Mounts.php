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
 * Mounts.
 *
 * @ORM\Table(name="mounts")
 * @ORM\Entity(repositoryClass="Lotgd\Core\EntityRepository\MountsRepository")
 */
class Mounts
{
    /**
     * @var int
     *
     * @ORM\Column(name="mountid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mountid;

    /**
     * @var string
     *
     * @ORM\Column(name="mountname", type="string", length=50, nullable=false)
     */
    private $mountname;

    /**
     * @var string
     *
     * @ORM\Column(name="mountdesc", type="text", length=65535, nullable=true)
     */
    private $mountdesc;

    /**
     * @var string
     *
     * @ORM\Column(name="mountcategory", type="string", length=50, nullable=false)
     */
    private $mountcategory;

    /**
     * @var string
     *
     * @ORM\Column(name="mountbuff", type="array", nullable=false)
     */
    private $mountbuff;

    /**
     * @var int
     *
     * @ORM\Column(name="mountcostgems", type="integer", nullable=false, options={"unsigned": true, "default": "0"})
     */
    private $mountcostgems = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="mountcostgold", type="integer", nullable=false, options={"unsigned": true, "default": "0"})
     */
    private $mountcostgold = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="mountactive", type="integer", nullable=false, options={"unsigned": true, "default": "1"})
     */
    private $mountactive = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="mountforestfights", type="integer", nullable=false, options={"default": "0"})
     */
    private $mountforestfights = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="newday", type="text", length=65535, nullable=false)
     */
    private $newday;

    /**
     * @var string
     *
     * @ORM\Column(name="recharge", type="text", length=65535, nullable=false)
     */
    private $recharge;

    /**
     * @var string
     *
     * @ORM\Column(name="partrecharge", type="text", length=65535, nullable=false)
     */
    private $partrecharge;

    /**
     * @var int
     *
     * @ORM\Column(name="mountfeedcost", type="integer", nullable=false, options={"unsigned": true, "default": "20"})
     */
    private $mountfeedcost = 20;

    /**
     * @var string
     *
     * @ORM\Column(name="mountlocation", type="string", length=25, nullable=false, options={"default": "all"})
     */
    private $mountlocation = 'all';

    /**
     * @var int
     *
     * @ORM\Column(name="mountdkcost", type="integer", nullable=false, options={"unsigned": true, "default": "0"})
     */
    private $mountdkcost = 0;

    /**
     * Set the value of Mountid.
     *
     * @param int mountid
     *
     * @return self
     */
    public function setMountid($mountid)
    {
        $this->mountid = $mountid;

        return $this;
    }

    /**
     * Get the value of Mountid.
     *
     * @return int
     */
    public function getMountid(): int
    {
        return $this->mountid;
    }

    /**
     * Set the value of Mountname.
     *
     * @param string mountname
     *
     * @return self
     */
    public function setMountname($mountname)
    {
        $this->mountname = $mountname;

        return $this;
    }

    /**
     * Get the value of Mountname.
     *
     * @return string
     */
    public function getMountname(): string
    {
        return $this->mountname;
    }

    /**
     * Set the value of Mountdesc.
     *
     * @param string mountdesc
     *
     * @return self
     */
    public function setMountdesc($mountdesc)
    {
        $this->mountdesc = $mountdesc;

        return $this;
    }

    /**
     * Get the value of Mountdesc.
     *
     * @return string
     */
    public function getMountdesc(): string
    {
        return $this->mountdesc;
    }

    /**
     * Set the value of Mountcategory.
     *
     * @param string mountcategory
     *
     * @return self
     */
    public function setMountcategory($mountcategory)
    {
        $this->mountcategory = $mountcategory;

        return $this;
    }

    /**
     * Get the value of Mountcategory.
     *
     * @return string
     */
    public function getMountcategory(): string
    {
        return $this->mountcategory;
    }

    /**
     * Set the value of Mountbuff.
     *
     * @param array mountbuff
     *
     * @return self
     */
    public function setMountbuff($mountbuff)
    {
        $this->mountbuff = $mountbuff;

        return $this;
    }

    /**
     * Get the value of Mountbuff.
     *
     * @return array
     */
    public function getMountbuff(): array
    {
        return $this->mountbuff;
    }

    /**
     * Set the value of Mountcostgems.
     *
     * @param int mountcostgems
     *
     * @return self
     */
    public function setMountcostgems($mountcostgems)
    {
        $this->mountcostgems = $mountcostgems;

        return $this;
    }

    /**
     * Get the value of Mountcostgems.
     *
     * @return int
     */
    public function getMountcostgems(): int
    {
        return $this->mountcostgems;
    }

    /**
     * Set the value of Mountcostgold.
     *
     * @param int mountcostgold
     *
     * @return self
     */
    public function setMountcostgold($mountcostgold)
    {
        $this->mountcostgold = $mountcostgold;

        return $this;
    }

    /**
     * Get the value of Mountcostgold.
     *
     * @return int
     */
    public function getMountcostgold(): int
    {
        return $this->mountcostgold;
    }

    /**
     * Set the value of Mountactive.
     *
     * @param int mountactive
     *
     * @return self
     */
    public function setMountactive($mountactive)
    {
        $this->mountactive = $mountactive;

        return $this;
    }

    /**
     * Get the value of Mountactive.
     *
     * @return int
     */
    public function getMountactive(): int
    {
        return $this->mountactive;
    }

    /**
     * Set the value of Mountforestfights.
     *
     * @param int mountforestfights
     *
     * @return self
     */
    public function setMountforestfights($mountforestfights)
    {
        $this->mountforestfights = $mountforestfights;

        return $this;
    }

    /**
     * Get the value of Mountforestfights.
     *
     * @return int
     */
    public function getMountforestfights(): int
    {
        return $this->mountforestfights;
    }

    /**
     * Set the value of Newday.
     *
     * @param string newday
     *
     * @return self
     */
    public function setNewday($newday)
    {
        $this->newday = $newday;

        return $this;
    }

    /**
     * Get the value of Newday.
     *
     * @return string
     */
    public function getNewday(): string
    {
        return $this->newday;
    }

    /**
     * Set the value of Recharge.
     *
     * @param string recharge
     *
     * @return self
     */
    public function setRecharge($recharge)
    {
        $this->recharge = $recharge;

        return $this;
    }

    /**
     * Get the value of Recharge.
     *
     * @return string
     */
    public function getRecharge(): string
    {
        return $this->recharge;
    }

    /**
     * Set the value of Partrecharge.
     *
     * @param string partrecharge
     *
     * @return self
     */
    public function setPartrecharge($partrecharge)
    {
        $this->partrecharge = $partrecharge;

        return $this;
    }

    /**
     * Get the value of Partrecharge.
     *
     * @return string
     */
    public function getPartrecharge(): string
    {
        return $this->partrecharge;
    }

    /**
     * Set the value of Mountfeedcost.
     *
     * @param int mountfeedcost
     *
     * @return self
     */
    public function setMountfeedcost($mountfeedcost)
    {
        $this->mountfeedcost = $mountfeedcost;

        return $this;
    }

    /**
     * Get the value of Mountfeedcost.
     *
     * @return int
     */
    public function getMountfeedcost(): int
    {
        return $this->mountfeedcost;
    }

    /**
     * Set the value of Mountlocation.
     *
     * @param string mountlocation
     *
     * @return self
     */
    public function setMountlocation($mountlocation)
    {
        $this->mountlocation = $mountlocation;

        return $this;
    }

    /**
     * Get the value of Mountlocation.
     *
     * @return string
     */
    public function getMountlocation(): string
    {
        return $this->mountlocation;
    }

    /**
     * Set the value of Mountdkcost.
     *
     * @param int mountdkcost
     *
     * @return self
     */
    public function setMountdkcost($mountdkcost)
    {
        $this->mountdkcost = $mountdkcost;

        return $this;
    }

    /**
     * Get the value of Mountdkcost.
     *
     * @return int
     */
    public function getMountdkcost(): int
    {
        return $this->mountdkcost;
    }
}
