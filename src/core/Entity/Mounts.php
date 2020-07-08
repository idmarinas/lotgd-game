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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mounts.
 *
 * @ORM\Table(name="mounts")
 * @ORM\Entity(repositoryClass="Lotgd\Core\EntityRepository\MountsRepository")
 * @Gedmo\TranslationEntity(class="Lotgd\Core\Entity\MountsTranslation")
 */
class Mounts implements Translatable
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
     * @Gedmo\Translatable
     * @ORM\Column(name="mountname", type="string", length=50, nullable=false)
     *
     * @Assert\Length(
     *     min=1,
     *     max=50,
     *     allowEmptyString=false
     * )
     */
    private $mountname;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="mountdesc", type="text", length=65535, nullable=true)
     *
     * @Assert\Length(
     *     min=1,
     *     max=65535,
     *     allowEmptyString=false
     * )
     */
    private $mountdesc;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="mountcategory", type="string", length=50, nullable=false)
     *
     * @Assert\Length(
     *     min=1,
     *     max=50,
     *     allowEmptyString=false
     * )
     */
    private $mountcategory;

    /**
     * @var array
     *
     * @ORM\Column(name="mountbuff", type="array", nullable=false)
     */
    private $mountbuff = [];

    /**
     * @var int
     *
     * @ORM\Column(name="mountcostgems", type="integer", nullable=false, options={"unsigned": true, "default": "0"})
     *
     * @Assert\Range(
     *     min=0,
     *     max=42949672295
     * )
     * @Assert\DivisibleBy(1)
     */
    private $mountcostgems = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="mountcostgold", type="integer", nullable=false, options={"unsigned": true, "default": "0"})
     *
     * @Assert\Range(
     *     min=0,
     *     max=42949672295
     * )
     * @Assert\DivisibleBy(1)
     */
    private $mountcostgold = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="mountactive", type="boolean", nullable=false, options={"default": "1"})
     */
    private $mountactive = true;

    /**
     * @var int
     *
     * @ORM\Column(name="mountforestfights", type="integer", nullable=false, options={"default": "0"})
     *
     * @Assert\Range(
     *     min=0,
     *     max=42949672295
     * )
     * @Assert\DivisibleBy(1)
     */
    private $mountforestfights = 0;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="newday", type="text", length=65535, nullable=false)
     *
     * @Assert\Length(
     *     min=1,
     *     max=65535,
     *     allowEmptyString=false
     * )
     */
    private $newday;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="recharge", type="text", length=65535, nullable=false)
     *
     * @Assert\Length(
     *     min=1,
     *     max=65535,
     *     allowEmptyString=false
     * )
     */
    private $recharge;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="partrecharge", type="text", length=65535, nullable=false)
     *
     * @Assert\Length(
     *     min=1,
     *     max=65535,
     *     allowEmptyString=false
     * )
     */
    private $partrecharge;

    /**
     * @var int
     *
     * @ORM\Column(name="mountfeedcost", type="integer", nullable=false, options={"unsigned": true, "default": "20"})
     *
     * @Assert\Range(
     *     min=0,
     *     max=42949672295
     * )
     * @Assert\DivisibleBy(1)
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
     *
     * @Assert\Range(
     *     min=0,
     *     max=42949672295
     * )
     * @Assert\DivisibleBy(1)
     */
    private $mountdkcost = 0;

    /**
     * @ORM\OneToMany(targetEntity="MountsTranslation", mappedBy="object", cascade={"all"})
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getMountid();
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(MountsTranslation $t)
    {
        if (! $this->translations->contains($t))
        {
            $t->setObject($this);
            $this->translations->add($t);
        }
    }

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
     */
    public function getMountbuff(): array
    {
        if (is_string($this->mountbuff))
        {
            $this->mountbuff = unserialize($this->mountbuff);
        }

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
     */
    public function getMountcostgold(): int
    {
        return $this->mountcostgold;
    }

    /**
     * Set the value of Mountactive.
     *
     * @param bool mountactive
     *
     * @return self
     */
    public function setMountactive(bool $mountactive)
    {
        $this->mountactive = $mountactive;

        return $this;
    }

    /**
     * Get the value of Mountactive.
     */
    public function getMountactive(): bool
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
     */
    public function getMountdkcost(): int
    {
        return $this->mountdkcost;
    }
}
