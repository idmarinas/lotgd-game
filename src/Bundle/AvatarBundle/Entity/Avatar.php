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

namespace Lotgd\Bundle\AvatarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lotgd\Bundle\CoreBundle\Entity\Common as CoreCommon;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Structure of table "avatar" in data base.
 *
 * This table store avatar info of users, only data related to character.
 *
 * @ORM\Table(
 *     indexes={
 *         @ORM\Index(name="name", columns={"name"}),
 *         @ORM\Index(name="level", columns={"level"}),
 *         @ORM\Index(name="lasthit", columns={"lasthit"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Lotgd\Bundle\AvatarBundle\Repository\AvatarRepository")
 *
 * @UniqueEntity(fields={"name"}, message="lotgd_avatar.form.create_avatar.field.name.duplicate")
 */
class Avatar
{
    use CoreCommon\IdTrait;
    use CoreCommon\Clan;
    use CoreCommon\User;
    use Avatar\Attributes;
    use Avatar\Dragon;
    use Avatar\Hitpoints;

    /**
     * @ORM\ManyToOne(targetEntity="Lotgd\Bundle\UserBundle\Entity\User", inversedBy="avatars")
     */
    protected $user;

    /**
     * @ORM\Column(name="name", type="string", unique=true, length=50)
     *
     * @Assert\Length(
     *     min=3,
     *     max=40
     * )
     * @Assert\NotEqualTo(propertyPath="user.getUsername")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="sex", type="smallint", options={"default": 0})
     */
    private $sex = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="experience", type="integer", options={"default": 0, "unsigned": true})
     */
    private $experience = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="gold", type="integer", options={"default": 0, "unsigned": true})
     */
    private $gold = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="seenmaster", type="boolean", nullable=false, options={"default": 0})
     */
    private $seenmaster = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false, options={"default": 1, "unsigned": true})
     */
    private $level = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="spirits", type="integer", nullable=false)
     */
    private $spirits = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"default": 0})
     */
    private $gems = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"default": 10})
     */
    private $turns = 10;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $title = '';

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $resurrections = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="age", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $age = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="charm", type="integer", nullable=false, options={"default": 0})
     */
    private $charm = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastmotd", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $lastmotd;

    /**
     * @var int
     *
     * @ORM\Column(name="playerfights", type="integer", nullable=false, options={"default": 3})
     */
    private $playerfights = 3;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lasthit", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $lasthit;

    /**
     * @var string
     *
     * @ORM\Column(name="restorepage", type="string", length=150, nullable=false)
     */
    private $restorepage = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="boughtroomtoday", type="boolean", nullable=false, options={"default": 0})
     */
    private $boughtroomtoday = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="sentnotice", type="boolean", nullable=false, options={"default": 0})
     */
    private $sentnotice = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pvpflag", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $pvpflag;

    /**
     * @var int
     *
     * @ORM\Column(name="transferredtoday", type="smallint", nullable=false, options={"default": 0})
     */
    private $transferredtoday = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="soulpoints", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $soulpoints = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="gravefights", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $gravefights = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="hauntedby", type="string", length=50, nullable=false)
     */
    private $hauntedby = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="recentcomments", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $recentcomments;

    /**
     * @var string
     *
     * @ORM\Column(name="bio", type="string", length=255, nullable=false)
     */
    private $bio = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $bioTime;

    /**
     * @var int
     *
     * @ORM\Column(name="amountouttoday", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $amountouttoday = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="pk", type="boolean", nullable=false, options={"default": 0})
     */
    private $pk = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="dragonage", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $dragonage = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="bestdragonage", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $bestdragonage = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="ctitle", type="string", length=25, nullable=false)
     */
    private $ctitle = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="slaydragon", type="boolean", nullable=false, options={"default": 0})
     */
    private $slaydragon = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="clanrank", type="smallint", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $clanrank = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="clanjoindate", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $clanjoindate;

    /**
     * Configure some default values.
     */
    public function __construct()
    {
        $this->lastmotd       = new \DateTime('0000-00-00 00:00:00');
        $this->lasthit        = new \DateTime('0000-00-00 00:00:00');
        $this->pvpflag        = new \DateTime('0000-00-00 00:00:00');
        $this->recentcomments = new \DateTime('0000-00-00 00:00:00');
        $this->bioTime        = new \DateTime('0000-00-00 00:00:00');
        $this->clanjoindate   = new \DateTime('0000-00-00 00:00:00');
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get name composite of title + name.
     */
    public function getPlayerName(): string
    {
        return $this->getTitle().' '.$this->getName();
    }

    /**
     * Set the value of Sex.
     *
     * @param int $sex
     *
     * @return self
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get the value of Sex.
     */
    public function getSex(): int
    {
        return $this->sex;
    }

    /**
     * Set the value of Experience.
     *
     * @param int $experience
     *
     * @return self
     */
    public function setExperience($experience)
    {
        $this->experience = (int) $experience;

        return $this;
    }

    /**
     * Get the value of Experience.
     */
    public function getExperience(): int
    {
        return $this->experience;
    }

    /**
     * Set the value of Gold.
     *
     * @param int $gold
     *
     * @return self
     */
    public function setGold($gold)
    {
        $this->gold = (int) $gold;

        return $this;
    }

    /**
     * Get the value of Gold.
     */
    public function getGold(): int
    {
        return $this->gold;
    }

    /**
     * Set the value of Seenmaster.
     *
     * @param bool $seenmaster
     *
     * @return self
     */
    public function setSeenmaster($seenmaster)
    {
        $this->seenmaster = $seenmaster;

        return $this;
    }

    /**
     * Get the value of Seenmaster.
     */
    public function getSeenmaster(): bool
    {
        return $this->seenmaster;
    }

    /**
     * Set the value of Level.
     *
     * @param int $level
     *
     * @return self
     */
    public function setLevel($level)
    {
        $this->level = (int) $level;

        return $this;
    }

    /**
     * Get the value of Level.
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * Set the value of Spirits.
     *
     * @param int $spirits
     *
     * @return self
     */
    public function setSpirits($spirits)
    {
        $this->spirits = $spirits;

        return $this;
    }

    /**
     * Get the value of Spirits.
     */
    public function getSpirits(): int
    {
        return $this->spirits;
    }

    /**
     * Set the value of Gems.
     *
     * @param int $gems
     *
     * @return self
     */
    public function setGems($gems)
    {
        $this->gems = (int) $gems;

        return $this;
    }

    /**
     * Get the value of Gems.
     */
    public function getGems(): int
    {
        return $this->gems;
    }

    /**
     * Set the value of Turns.
     *
     * @param int $turns
     *
     * @return self
     */
    public function setTurns($turns)
    {
        $this->turns = (int) $turns;

        return $this;
    }

    /**
     * Get the value of Turns.
     */
    public function getTurns(): int
    {
        return $this->turns;
    }

    /**
     * Set the value of Title.
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of Title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the value of Resurrections.
     *
     * @param int $resurrections
     *
     * @return self
     */
    public function setResurrections($resurrections)
    {
        $this->resurrections = (int) $resurrections;

        return $this;
    }

    /**
     * Get the value of Resurrections.
     */
    public function getResurrections(): int
    {
        return $this->resurrections;
    }

    /**
     * Set the value of Age.
     *
     * @param int $age
     *
     * @return self
     */
    public function setAge($age)
    {
        $this->age = (int) $age;

        return $this;
    }

    /**
     * Get the value of Age.
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * Set the value of Charm.
     *
     * @param int $charm
     *
     * @return self
     */
    public function setCharm($charm)
    {
        $this->charm = (int) $charm;

        return $this;
    }

    /**
     * Get the value of Charm.
     */
    public function getCharm(): int
    {
        return $this->charm;
    }

    /**
     * Set the value of Lastmotd.
     *
     * @return self
     */
    public function setLastmotd(\DateTime $lastmotd)
    {
        $this->lastmotd = $lastmotd;

        return $this;
    }

    /**
     * Get the value of Lastmotd.
     */
    public function getLastmotd(): \DateTime
    {
        return $this->lastmotd;
    }

    /**
     * Set the value of Playerfights.
     *
     * @param int $playerfights
     *
     * @return self
     */
    public function setPlayerfights($playerfights)
    {
        $this->playerfights = (int) $playerfights;

        return $this;
    }

    /**
     * Get the value of Playerfights.
     */
    public function getPlayerfights(): int
    {
        return $this->playerfights;
    }

    /**
     * Set the value of Lasthit.
     *
     * @return self
     */
    public function setLasthit(\DateTime $lasthit)
    {
        $this->lasthit = $lasthit;

        return $this;
    }

    /**
     * Get the value of Lasthit.
     */
    public function getLasthit(): \DateTime
    {
        return $this->lasthit;
    }


    /**
     * Set the value of Restorepage.
     *
     * @param string $restorepage
     *
     * @return self
     */
    public function setRestorepage($restorepage)
    {
        $this->restorepage = $restorepage;

        return $this;
    }

    /**
     * Get the value of Restorepage.
     */
    public function getRestorepage(): string
    {
        return $this->restorepage;
    }

    /**
     * Set the value of Boughtroomtoday.
     *
     * @param bool $boughtroomtoday
     *
     * @return self
     */
    public function setBoughtroomtoday($boughtroomtoday)
    {
        $this->boughtroomtoday = $boughtroomtoday;

        return $this;
    }

    /**
     * Get the value of Boughtroomtoday.
     */
    public function getBoughtroomtoday(): bool
    {
        return $this->boughtroomtoday;
    }

    /**
     * Set the value of Sentnotice.
     *
     * @param bool $sentnotice
     *
     * @return self
     */
    public function setSentnotice($sentnotice)
    {
        $this->sentnotice = $sentnotice;

        return $this;
    }

    /**
     * Get the value of Sentnotice.
     */
    public function getSentnotice(): bool
    {
        return $this->sentnotice;
    }

    /**
     * Set the value of Pvpflag.
     *
     * @return self
     */
    public function setPvpflag(\DateTime $pvpflag)
    {
        $this->pvpflag = $pvpflag;

        return $this;
    }

    /**
     * Get the value of Pvpflag.
     */
    public function getPvpflag(): \DateTime
    {
        return $this->pvpflag;
    }

    /**
     * Set the value of Transferredtoday.
     *
     * @param int $transferredtoday
     *
     * @return self
     */
    public function setTransferredtoday($transferredtoday)
    {
        $this->transferredtoday = (int) $transferredtoday;

        return $this;
    }

    /**
     * Get the value of Transferredtoday.
     */
    public function getTransferredtoday(): int
    {
        return $this->transferredtoday;
    }

    /**
     * Set the value of Soulpoints.
     *
     * @param int $soulpoints
     *
     * @return self
     */
    public function setSoulpoints($soulpoints)
    {
        $this->soulpoints = (int) $soulpoints;

        return $this;
    }

    /**
     * Get the value of Soulpoints.
     */
    public function getSoulpoints(): int
    {
        return $this->soulpoints;
    }

    /**
     * Set the value of Gravefights.
     *
     * @param int $gravefights
     *
     * @return self
     */
    public function setGravefights($gravefights)
    {
        $this->gravefights = (int) $gravefights;

        return $this;
    }

    /**
     * Get the value of Gravefights.
     */
    public function getGravefights(): int
    {
        return $this->gravefights;
    }

    /**
     * Set the value of Hauntedby.
     *
     * @param string $hauntedby
     *
     * @return self
     */
    public function setHauntedby($hauntedby)
    {
        $this->hauntedby = $hauntedby;

        return $this;
    }

    /**
     * Get the value of Hauntedby.
     */
    public function getHauntedby(): string
    {
        return $this->hauntedby;
    }

    /**
     * Set the value of Recentcomments.
     *
     * @return self
     */
    public function setRecentcomments(\DateTime $recentcomments)
    {
        $this->recentcomments = $recentcomments;

        return $this;
    }

    /**
     * Get the value of Recentcomments.
     */
    public function getRecentcomments(): \DateTime
    {
        return $this->recentcomments;
    }

    /**
     * Set the value of Bio.
     *
     * @return self
     */
    public function setBio(string $bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get the value of Bio.
     */
    public function getBio(): string
    {
        return $this->bio;
    }

    /**
     * Set the value of BioTime.
     *
     * @return self
     */
    public function setBioTime(\DateTime $bioTime)
    {
        $this->bioTime = $bioTime;

        return $this;
    }

    /**
     * Get the value of BioTime.
     */
    public function getBioTime(): \DateTime
    {
        return $this->bioTime;
    }

    /**
     * Set the value of Amountouttoday.
     *
     * @param int $amountouttoday
     *
     * @return self
     */
    public function setAmountouttoday($amountouttoday)
    {
        $this->amountouttoday = (int) $amountouttoday;

        return $this;
    }

    /**
     * Get the value of Amountouttoday.
     */
    public function getAmountouttoday(): int
    {
        return $this->amountouttoday;
    }

    /**
     * Set the value of Pk.
     *
     * @param bool $pk
     *
     * @return self
     */
    public function setPk($pk)
    {
        $this->pk = $pk;

        return $this;
    }

    /**
     * Get the value of Pk.
     */
    public function getPk(): bool
    {
        return $this->pk;
    }

    /**
     * Set the value of Dragonage.
     *
     * @param int $dragonage
     *
     * @return self
     */
    public function setDragonage($dragonage)
    {
        $this->dragonage = (int) $dragonage;

        return $this;
    }

    /**
     * Get the value of Dragonage.
     */
    public function getDragonage(): int
    {
        return $this->dragonage;
    }

    /**
     * Set the value of Bestdragonage.
     *
     * @param int $bestdragonage
     *
     * @return self
     */
    public function setBestdragonage($bestdragonage)
    {
        $this->bestdragonage = (int) $bestdragonage;

        return $this;
    }

    /**
     * Get the value of Bestdragonage.
     */
    public function getBestdragonage(): int
    {
        return $this->bestdragonage;
    }

    /**
     * Set the value of Ctitle.
     *
     * @param string $ctitle
     *
     * @return self
     */
    public function setCtitle($ctitle)
    {
        $this->ctitle = $ctitle;

        return $this;
    }

    /**
     * Get the value of Ctitle.
     */
    public function getCtitle(): string
    {
        return $this->ctitle;
    }

    /**
     * Set the value of Slaydragon.
     *
     * @param bool $slaydragon
     *
     * @return self
     */
    public function setSlaydragon($slaydragon)
    {
        $this->slaydragon = $slaydragon;

        return $this;
    }

    /**
     * Get the value of Slaydragon.
     */
    public function getSlaydragon(): bool
    {
        return $this->slaydragon;
    }

    /**
     * Set the value of Clanrank.
     *
     * @param int $clanrank
     *
     * @return self
     */
    public function setClanrank($clanrank)
    {
        $this->clanrank = (int) $clanrank;

        return $this;
    }

    /**
     * Get the value of Clanrank.
     */
    public function getClanrank(): int
    {
        return $this->clanrank;
    }

    /**
     * Set the value of Clanjoindate.
     *
     * @return self
     */
    public function setClanjoindate(\DateTime $clanjoindate)
    {
        $this->clanjoindate = $clanjoindate;

        return $this;
    }

    /**
     * Get the value of Clanjoindate.
     */
    public function getClanjoindate(): \DateTime
    {
        return $this->clanjoindate;
    }
}
