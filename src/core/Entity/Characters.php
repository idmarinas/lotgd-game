<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Characters.
 *
 * @ORM\Table(name="characters",
 *      indexes={
 *          @ORM\Index(name="name", columns={"name"}),
 *          @ORM\Index(name="level", columns={"level"}),
 *          @ORM\Index(name="alive", columns={"alive"}),
 *          @ORM\Index(name="lasthit", columns={"lasthit"}),
 *          @ORM\Index(name="clanid", columns={"clanid"})
 *      }
 * )
 * @ORM\Entity
 */
class Characters
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\OneToOne(targetEntity="Accounts")
     * @ORM\JoinColumn(referencedColumnName="acctid", nullable=false, onDelete="CASCADE")
     */
    private $acct;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string" length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="playername", type="string", unique=true, length=40, nullable=false)
     */
    private $playername;

    /**
     * @var bool
     *
     * @ORM\Column(name="sex", type="boolean", nullable=false, options={"default":0})
     */
    private $sex = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="strength", type="smallint", nullable=false, options={"default":10, "unsigned":true})
     */
    private $strength = 10;

    /**
     * @var int
     *
     * @ORM\Column(name="dexterity", type="smallint", nullable=false, options={"default":10, "unsigned":true})
     */
    private $dexterity = 10;

    /**
     * @var int
     *
     * @ORM\Column(name="intelligence", type="smallint", nullable=false, options={"default":10, "unsigned":true})
     */
    private $intelligence = 10;

    /**
     * @var int
     *
     * @ORM\Column(name="constitution", type="smallint", nullable=false, options={"default":10, "unsigned":true})
     */
    private $constitution = 10;

    /**
     * @var int
     *
     * @ORM\Column(name="wisdom", type="smallint", nullable=false, options={"default":10, "unsigned":true})
     */
    private $wisdom = 10;

    /**
     * @var string
     *
     * @ORM\Column(name="specialty", type="string", length=20, nullable=false)
     */
    private $specialty = '';

    /**
     * @var int
     *
     * @ORM\Column(name="experience", type="bigint", nullable=false, options={"default":0, "unsigned":true})
     */
    private $experience = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="gold", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $gold = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="weapon", type="string", length=50, nullable=false, options={"default":"Fists"})
     */
    private $weapon = 'Fists';

    /**
     * @var string
     *
     * @ORM\Column(name="armor", type="string", length=50, nullable=false, options={"default":"T-Shirt"})
     */
    private $armor = 'T-Shirt';

    /**
     * @var bool
     *
     * @ORM\Column(name="seenmaster", type="boolean", nullable=false, options={"default":0})
     */
    private $seenmaster = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="smallint", nullable=false, options={"default":1, "unsigned":true})
     */
    private $level = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="defense", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $defense = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="attack", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $attack = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="alive", type="boolean", nullable=false, options={"default":1})
     */
    private $alive = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="goldinbank", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $goldinbank = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="marriedto", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $marriedto = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="spirits", type="integer", nullable=false)
     */
    private $spirits = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="hitpoints", type="integer", nullable=false, options={"default":10, "unsigned":true})
     */
    private $hitpoints = 10;

    /**
     * @var int
     *
     * @ORM\Column(name="maxhitpoints", type="integer", nullable=false, options={"default":10, "unsigned":true})
     */
    private $maxhitpoints = 10;

    /**
     * @var int
     *
     * @ORM\Column(name="permahitpoints", type="integer", nullable=false, options={"default":0})
     */
    private $permahitpoints = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="gems", type="integer", nullable=false, options={"default":0})
     */
    private $gems = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="weaponvalue", type="integer", nullable=false, options={"default":0})
     */
    private $weaponvalue = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="armorvalue", type="integer", nullable=false, options={"default":0})
     */
    private $armorvalue = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=50, nullable=false, options={"default":"Degolburg"})
     */
    private $location = 'Degolburg';

    /**
     * @var int
     *
     * @ORM\Column(name="turns", type="integer", nullable=false, options={"default":10})
     */
    private $turns = 10;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(name="badguy", type="array", nullable=false)
     */
    private $badguy;

    /**
     * @var string
     *
     * @ORM\Column(name="companions", type="array", nullable=false)
     */
    private $companions;

    /**
     * @var string
     *
     * @ORM\Column(name="allowednavs", type="array", nullable=false)
     */
    private $allowednavs;

    /**
     * @var int
     *
     * @ORM\Column(name="resurrections", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $resurrections = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="weapondmg", type="integer", nullable=false)
     */
    private $weapondmg = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="armordef", type="integer", nullable=false)
     */
    private $armordef = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="age", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $age = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="charm", type="integer", nullable=false, options={"default":0})
     */
    private $charm = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="specialinc", type="string", length=50, nullable=false)
     */
    private $specialinc = '';

    /**
     * @var string
     *
     * @ORM\Column(name="specialmisc", type="string", length=1000, nullable=false)
     */
    private $specialmisc = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastmotd", type="datetime", nullable=false, options={"default":"0000-00-00 00:00:00"})
     */
    private $lastmotd;

    /**
     * @var int
     *
     * @ORM\Column(name="playerfights", type="integer", nullable=false, options={"default":3})
     */
    private $playerfights = 3;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lasthit", type="datetime", nullable=false, options={"default":"0000-00-00 00:00:00"})
     */
    private $lasthit;

    /**
     * @var bool
     *
     * @ORM\Column(name="seendragon", type="boolean", nullable=false, options={"default":0})
     */
    private $seendragon = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="dragonkills", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $dragonkills = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="restorepage", type="string", length=150, nullable=false)
     */
    private $restorepage = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="hashorse", type="boolean", nullable=false, options={"default":0})
     */
    private $hashorse = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="bufflist", type="array", nullable=false)
     */
    private $bufflist;

    /**
     * @var string
     *
     * @ORM\Column(name="dragonpoints", type="array", nullable=false)
     */
    private $dragonpoints;

    /**
     * @var bool
     *
     * @ORM\Column(name="boughtroomtoday", type="boolean", nullable=false, options={"default":0})
     */
    private $boughtroomtoday = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="sentnotice", type="boolean", nullable=false, options={"default":0})
     */
    private $sentnotice = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pvpflag", type="datetime", nullable=false, options={"default":"0000-00-00 00:00:00"})
     */
    private $pvpflag;

    /**
     * @var int
     *
     * @ORM\Column(name="transferredtoday", type="smallint", nullable=false, options={"default":0, "unsigned":true})
     */
    private $transferredtoday = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="soulpoints", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $soulpoints = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="gravefights", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $gravefights = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="hauntedby", type="string", length=50, nullable=false)
     */
    private $hauntedby = '';

    /**
     * @var int
     *
     * @ORM\Column(name="deathpower", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $deathpower = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="recentcomments", type="datetime", nullable=false, options={"default":"0000-00-00 00:00:00"})
     */
    private $recentcomments;

    /**
     * @var string
     *
     * @ORM\Column(name="bio", type="string", length=255, nullable=false)
     */
    private $bio = '';

    /**
     * @var string
     *
     * @ORM\Column(name="race", type="string", length=50, nullable=false, options={"default":0})
     */
    private $race = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="biotime", type="datetime", nullable=false, options={"default":"0000-00-00 00:00:00"})
     */
    private $biotime;

    /**
     * @var int
     *
     * @ORM\Column(name="amountouttoday", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $amountouttoday = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="pk", type="boolean", nullable=false, options={"default":0})
     */
    private $pk = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="dragonage", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $dragonage = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="bestdragonage", type="integer", nullable=false, options={"default":0, "unsigned":true})
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
     * @ORM\Column(name="slaydragon", type="boolean", nullable=false, options={"default":0})
     */
    private $slaydragon = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="fedmount", type="boolean", nullable=false, options={"default":0})
     */
    private $fedmount = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="clanid", type="integer", nullable=false, options={"default":0, "unsigned":true})
     */
    private $clanid = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="clanrank", type="boolean", nullable=false, options={"default":0})
     */
    private $clanrank = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="clanjoindate", type="datetime", nullable=false, options={"default":"0000-00-00 00:00:00"})
     */
    private $clanjoindate;

    /**
     * @var string
     *
     * @ORM\Column(name="chatloc", type="string", length=255, nullable=false)
     */
    private $chatloc = '';

    /**
     * Configure same default values.
     */
    public function __construct()
    {
        $this->lastmotd = new \DateTime('0000-00-00 00:00:00');
        $this->lasthit = new \DateTime('0000-00-00 00:00:00');
        $this->pvpflag = new \DateTime('0000-00-00 00:00:00');
        $this->recentcomments = new \DateTime('0000-00-00 00:00:00');
        $this->biotime = new \DateTime('0000-00-00 00:00:00');
        $this->clanjoindate = new \DateTime('0000-00-00 00:00:00');
    }

    /**
     * Set the value of Id.
     *
     * @param int id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of Id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of Acct.
     *
     * @param int acct
     *
     * @return self
     */
    public function setAcct($acct)
    {
        $this->acct = $acct;

        return $this;
    }

    /**
     * Get the value of Acct.
     *
     * @return int
     */
    public function getAcct()
    {
        return $this->acct;
    }

    /**
     * Set the value of Name.
     *
     * @param string name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of Name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of Playername.
     *
     * @param string playername
     *
     * @return self
     */
    public function setPlayername($playername)
    {
        $this->playername = $playername;

        return $this;
    }

    /**
     * Get the value of Playername.
     *
     * @return string
     */
    public function getPlayername(): string
    {
        return $this->playername;
    }

    /**
     * Set the value of Sex.
     *
     * @param bool sex
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
     *
     * @return bool
     */
    public function getSex(): bool
    {
        return $this->sex;
    }

    /**
     * Set the value of Strength.
     *
     * @param int strength
     *
     * @return self
     */
    public function setStrength($strength)
    {
        $this->strength = $strength;

        return $this;
    }

    /**
     * Get the value of Strength.
     *
     * @return int
     */
    public function getStrength(): int
    {
        return $this->strength;
    }

    /**
     * Set the value of Dexterity.
     *
     * @param int dexterity
     *
     * @return self
     */
    public function setDexterity($dexterity)
    {
        $this->dexterity = $dexterity;

        return $this;
    }

    /**
     * Get the value of Dexterity.
     *
     * @return int
     */
    public function getDexterity(): int
    {
        return $this->dexterity;
    }

    /**
     * Set the value of Intelligence.
     *
     * @param int intelligence
     *
     * @return self
     */
    public function setIntelligence($intelligence)
    {
        $this->intelligence = $intelligence;

        return $this;
    }

    /**
     * Get the value of Intelligence.
     *
     * @return int
     */
    public function getIntelligence(): int
    {
        return $this->intelligence;
    }

    /**
     * Set the value of Constitution.
     *
     * @param int constitution
     *
     * @return self
     */
    public function setConstitution($constitution)
    {
        $this->constitution = $constitution;

        return $this;
    }

    /**
     * Get the value of Constitution.
     *
     * @return int
     */
    public function getConstitution(): int
    {
        return $this->constitution;
    }

    /**
     * Set the value of Wisdom.
     *
     * @param int wisdom
     *
     * @return self
     */
    public function setWisdom($wisdom)
    {
        $this->wisdom = $wisdom;

        return $this;
    }

    /**
     * Get the value of Wisdom.
     *
     * @return int
     */
    public function getWisdom(): int
    {
        return $this->wisdom;
    }

    /**
     * Set the value of Specialty.
     *
     * @param string specialty
     *
     * @return self
     */
    public function setSpecialty($specialty)
    {
        $this->specialty = $specialty;

        return $this;
    }

    /**
     * Get the value of Specialty.
     *
     * @return string
     */
    public function getSpecialty(): string
    {
        return $this->specialty;
    }

    /**
     * Set the value of Experience.
     *
     * @param int experience
     *
     * @return self
     */
    public function setExperience($experience)
    {
        $this->experience = $experience;

        return $this;
    }

    /**
     * Get the value of Experience.
     *
     * @return int
     */
    public function getExperience(): int
    {
        return $this->experience;
    }

    /**
     * Set the value of Gold.
     *
     * @param int gold
     *
     * @return self
     */
    public function setGold($gold)
    {
        $this->gold = $gold;

        return $this;
    }

    /**
     * Get the value of Gold.
     *
     * @return int
     */
    public function getGold(): int
    {
        return $this->gold;
    }

    /**
     * Set the value of Weapon.
     *
     * @param string weapon
     *
     * @return self
     */
    public function setWeapon($weapon)
    {
        $this->weapon = $weapon;

        return $this;
    }

    /**
     * Get the value of Weapon.
     *
     * @return string
     */
    public function getWeapon(): string
    {
        return $this->weapon;
    }

    /**
     * Set the value of Armor.
     *
     * @param string armor
     *
     * @return self
     */
    public function setArmor($armor)
    {
        $this->armor = $armor;

        return $this;
    }

    /**
     * Get the value of Armor.
     *
     * @return string
     */
    public function getArmor(): string
    {
        return $this->armor;
    }

    /**
     * Set the value of Seenmaster.
     *
     * @param bool seenmaster
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
     *
     * @return bool
     */
    public function getSeenmaster(): bool
    {
        return $this->seenmaster;
    }

    /**
     * Set the value of Level.
     *
     * @param int level
     *
     * @return self
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get the value of Level.
     *
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * Set the value of Defense.
     *
     * @param int defense
     *
     * @return self
     */
    public function setDefense($defense)
    {
        $this->defense = $defense;

        return $this;
    }

    /**
     * Get the value of Defense.
     *
     * @return int
     */
    public function getDefense(): int
    {
        return $this->defense;
    }

    /**
     * Set the value of Attack.
     *
     * @param int attack
     *
     * @return self
     */
    public function setAttack($attack)
    {
        $this->attack = $attack;

        return $this;
    }

    /**
     * Get the value of Attack.
     *
     * @return int
     */
    public function getAttack(): int
    {
        return $this->attack;
    }

    /**
     * Set the value of Alive.
     *
     * @param bool alive
     *
     * @return self
     */
    public function setAlive($alive)
    {
        $this->alive = $alive;

        return $this;
    }

    /**
     * Get the value of Alive.
     *
     * @return bool
     */
    public function getAlive(): bool
    {
        return $this->alive;
    }

    /**
     * Set the value of Goldinbank.
     *
     * @param int goldinbank
     *
     * @return self
     */
    public function setGoldinbank($goldinbank)
    {
        $this->goldinbank = $goldinbank;

        return $this;
    }

    /**
     * Get the value of Goldinbank.
     *
     * @return int
     */
    public function getGoldinbank(): int
    {
        return $this->goldinbank;
    }

    /**
     * Set the value of Marriedto.
     *
     * @param int marriedto
     *
     * @return self
     */
    public function setMarriedto($marriedto)
    {
        $this->marriedto = $marriedto;

        return $this;
    }

    /**
     * Get the value of Marriedto.
     *
     * @return int
     */
    public function getMarriedto(): int
    {
        return $this->marriedto;
    }

    /**
     * Set the value of Spirits.
     *
     * @param int spirits
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
     *
     * @return int
     */
    public function getSpirits(): int
    {
        return $this->spirits;
    }

    /**
     * Set the value of Hitpoints.
     *
     * @param int hitpoints
     *
     * @return self
     */
    public function setHitpoints($hitpoints)
    {
        $this->hitpoints = $hitpoints;

        return $this;
    }

    /**
     * Get the value of Hitpoints.
     *
     * @return int
     */
    public function getHitpoints(): int
    {
        return $this->hitpoints;
    }

    /**
     * Set the value of Maxhitpoints.
     *
     * @param int maxhitpoints
     *
     * @return self
     */
    public function setMaxhitpoints($maxhitpoints)
    {
        $this->maxhitpoints = $maxhitpoints;

        return $this;
    }

    /**
     * Get the value of Maxhitpoints.
     *
     * @return int
     */
    public function getMaxhitpoints(): int
    {
        return $this->maxhitpoints;
    }

    /**
     * Set the value of Permahitpoints.
     *
     * @param int permahitpoints
     *
     * @return self
     */
    public function setPermahitpoints($permahitpoints)
    {
        $this->permahitpoints = $permahitpoints;

        return $this;
    }

    /**
     * Get the value of Permahitpoints.
     *
     * @return int
     */
    public function getPermahitpoints(): int
    {
        return $this->permahitpoints;
    }

    /**
     * Set the value of Gems.
     *
     * @param int gems
     *
     * @return self
     */
    public function setGems($gems)
    {
        $this->gems = $gems;

        return $this;
    }

    /**
     * Get the value of Gems.
     *
     * @return int
     */
    public function getGems(): int
    {
        return $this->gems;
    }

    /**
     * Set the value of Weaponvalue.
     *
     * @param int weaponvalue
     *
     * @return self
     */
    public function setWeaponvalue($weaponvalue)
    {
        $this->weaponvalue = $weaponvalue;

        return $this;
    }

    /**
     * Get the value of Weaponvalue.
     *
     * @return int
     */
    public function getWeaponvalue(): int
    {
        return $this->weaponvalue;
    }

    /**
     * Set the value of Armorvalue.
     *
     * @param int armorvalue
     *
     * @return self
     */
    public function setArmorvalue($armorvalue)
    {
        $this->armorvalue = $armorvalue;

        return $this;
    }

    /**
     * Get the value of Armorvalue.
     *
     * @return int
     */
    public function getArmorvalue(): int
    {
        return $this->armorvalue;
    }

    /**
     * Set the value of Location.
     *
     * @param string location
     *
     * @return self
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get the value of Location.
     *
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Set the value of Turns.
     *
     * @param int turns
     *
     * @return self
     */
    public function setTurns($turns)
    {
        $this->turns = $turns;

        return $this;
    }

    /**
     * Get the value of Turns.
     *
     * @return int
     */
    public function getTurns(): int
    {
        return $this->turns;
    }

    /**
     * Set the value of Title.
     *
     * @param string title
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
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the value of Badguy.
     *
     * @param string badguy
     *
     * @return self
     */
    public function setBadguy($badguy)
    {
        $this->badguy = $badguy;

        return $this;
    }

    /**
     * Get the value of Badguy.
     *
     * @return string
     */
    public function getBadguy()
    {
        return $this->badguy;
    }

    /**
     * Set the value of Companions.
     *
     * @param string companions
     *
     * @return self
     */
    public function setCompanions($companions)
    {
        $this->companions = $companions;

        return $this;
    }

    /**
     * Get the value of Companions.
     *
     * @return string
     */
    public function getCompanions()
    {
        return $this->companions;
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
    public function getAllowednavs()
    {
        return $this->allowednavs;
    }

    /**
     * Set the value of Resurrections.
     *
     * @param int resurrections
     *
     * @return self
     */
    public function setResurrections($resurrections)
    {
        $this->resurrections = $resurrections;

        return $this;
    }

    /**
     * Get the value of Resurrections.
     *
     * @return int
     */
    public function getResurrections(): int
    {
        return $this->resurrections;
    }

    /**
     * Set the value of Weapondmg.
     *
     * @param int weapondmg
     *
     * @return self
     */
    public function setWeapondmg($weapondmg)
    {
        $this->weapondmg = $weapondmg;

        return $this;
    }

    /**
     * Get the value of Weapondmg.
     *
     * @return int
     */
    public function getWeapondmg(): int
    {
        return $this->weapondmg;
    }

    /**
     * Set the value of Armordef.
     *
     * @param int armordef
     *
     * @return self
     */
    public function setArmordef($armordef)
    {
        $this->armordef = $armordef;

        return $this;
    }

    /**
     * Get the value of Armordef.
     *
     * @return int
     */
    public function getArmordef(): int
    {
        return $this->armordef;
    }

    /**
     * Set the value of Age.
     *
     * @param int age
     *
     * @return self
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get the value of Age.
     *
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * Set the value of Charm.
     *
     * @param int charm
     *
     * @return self
     */
    public function setCharm($charm)
    {
        $this->charm = $charm;

        return $this;
    }

    /**
     * Get the value of Charm.
     *
     * @return int
     */
    public function getCharm(): int
    {
        return $this->charm;
    }

    /**
     * Set the value of Specialinc.
     *
     * @param string specialinc
     *
     * @return self
     */
    public function setSpecialinc($specialinc)
    {
        $this->specialinc = $specialinc;

        return $this;
    }

    /**
     * Get the value of Specialinc.
     *
     * @return string
     */
    public function getSpecialinc(): string
    {
        return $this->specialinc;
    }

    /**
     * Set the value of Specialmisc.
     *
     * @param string specialmisc
     *
     * @return self
     */
    public function setSpecialmisc($specialmisc)
    {
        $this->specialmisc = $specialmisc;

        return $this;
    }

    /**
     * Get the value of Specialmisc.
     *
     * @return string
     */
    public function getSpecialmisc(): string
    {
        return $this->specialmisc;
    }

    /**
     * Set the value of Lastmotd.
     *
     * @param \DateTime lastmotd
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
     *
     * @return \DateTime
     */
    public function getLastmotd(): \DateTime
    {
        return $this->lastmotd;
    }

    /**
     * Set the value of Playerfights.
     *
     * @param int playerfights
     *
     * @return self
     */
    public function setPlayerfights($playerfights)
    {
        $this->playerfights = $playerfights;

        return $this;
    }

    /**
     * Get the value of Playerfights.
     *
     * @return int
     */
    public function getPlayerfights(): int
    {
        return $this->playerfights;
    }

    /**
     * Set the value of Lasthit.
     *
     * @param \DateTime lasthit
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
     *
     * @return \DateTime
     */
    public function getLasthit(): \DateTime
    {
        return $this->lasthit;
    }

    /**
     * Set the value of Seendragon.
     *
     * @param bool seendragon
     *
     * @return self
     */
    public function setSeendragon($seendragon)
    {
        $this->seendragon = $seendragon;

        return $this;
    }

    /**
     * Get the value of Seendragon.
     *
     * @return bool
     */
    public function getSeendragon(): bool
    {
        return $this->seendragon;
    }

    /**
     * Set the value of Dragonkills.
     *
     * @param int dragonkills
     *
     * @return self
     */
    public function setDragonkills($dragonkills)
    {
        $this->dragonkills = $dragonkills;

        return $this;
    }

    /**
     * Get the value of Dragonkills.
     *
     * @return int
     */
    public function getDragonkills(): int
    {
        return $this->dragonkills;
    }

    /**
     * Set the value of Restorepage.
     *
     * @param string restorepage
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
     *
     * @return string
     */
    public function getRestorepage(): string
    {
        return $this->restorepage;
    }

    /**
     * Set the value of Hashorse.
     *
     * @param bool hashorse
     *
     * @return self
     */
    public function setHashorse($hashorse)
    {
        $this->hashorse = $hashorse;

        return $this;
    }

    /**
     * Get the value of Hashorse.
     *
     * @return bool
     */
    public function getHashorse(): bool
    {
        return $this->hashorse;
    }

    /**
     * Set the value of Bufflist.
     *
     * @param string bufflist
     *
     * @return self
     */
    public function setBufflist($bufflist)
    {
        $this->bufflist = $bufflist;

        return $this;
    }

    /**
     * Get the value of Bufflist.
     *
     * @return string
     */
    public function getBufflist()
    {
        return $this->bufflist;
    }

    /**
     * Set the value of Dragonpoints.
     *
     * @param string dragonpoints
     *
     * @return self
     */
    public function setDragonpoints($dragonpoints)
    {
        $this->dragonpoints = $dragonpoints;

        return $this;
    }

    /**
     * Get the value of Dragonpoints.
     *
     * @return string
     */
    public function getDragonpoints()
    {
        return $this->dragonpoints;
    }

    /**
     * Set the value of Boughtroomtoday.
     *
     * @param bool boughtroomtoday
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
     *
     * @return bool
     */
    public function getBoughtroomtoday(): bool
    {
        return $this->boughtroomtoday;
    }

    /**
     * Set the value of Sentnotice.
     *
     * @param bool sentnotice
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
     *
     * @return bool
     */
    public function getSentnotice(): bool
    {
        return $this->sentnotice;
    }

    /**
     * Set the value of Pvpflag.
     *
     * @param \DateTime pvpflag
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
     *
     * @return \DateTime
     */
    public function getPvpflag(): \DateTime
    {
        return $this->pvpflag;
    }

    /**
     * Set the value of Transferredtoday.
     *
     * @param int transferredtoday
     *
     * @return self
     */
    public function setTransferredtoday($transferredtoday)
    {
        $this->transferredtoday = $transferredtoday;

        return $this;
    }

    /**
     * Get the value of Transferredtoday.
     *
     * @return int
     */
    public function getTransferredtoday(): int
    {
        return $this->transferredtoday;
    }

    /**
     * Set the value of Soulpoints.
     *
     * @param int soulpoints
     *
     * @return self
     */
    public function setSoulpoints($soulpoints)
    {
        $this->soulpoints = $soulpoints;

        return $this;
    }

    /**
     * Get the value of Soulpoints.
     *
     * @return int
     */
    public function getSoulpoints(): int
    {
        return $this->soulpoints;
    }

    /**
     * Set the value of Gravefights.
     *
     * @param int gravefights
     *
     * @return self
     */
    public function setGravefights($gravefights)
    {
        $this->gravefights = $gravefights;

        return $this;
    }

    /**
     * Get the value of Gravefights.
     *
     * @return int
     */
    public function getGravefights(): int
    {
        return $this->gravefights;
    }

    /**
     * Set the value of Hauntedby.
     *
     * @param string hauntedby
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
     *
     * @return string
     */
    public function getHauntedby(): string
    {
        return $this->hauntedby;
    }

    /**
     * Set the value of Deathpower.
     *
     * @param int deathpower
     *
     * @return self
     */
    public function setDeathpower($deathpower)
    {
        $this->deathpower = $deathpower;

        return $this;
    }

    /**
     * Get the value of Deathpower.
     *
     * @return int
     */
    public function getDeathpower(): int
    {
        return $this->deathpower;
    }

    /**
     * Set the value of Recentcomments.
     *
     * @param \DateTime recentcomments
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
     *
     * @return \DateTime
     */
    public function getRecentcomments(): \DateTime
    {
        return $this->recentcomments;
    }

    /**
     * Set the value of Bio.
     *
     * @param string bio
     *
     * @return self
     */
    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get the value of Bio.
     *
     * @return string
     */
    public function getBio(): string
    {
        return $this->bio;
    }

    /**
     * Set the value of Race.
     *
     * @param string race
     *
     * @return self
     */
    public function setRace($race)
    {
        $this->race = $race;

        return $this;
    }

    /**
     * Get the value of Race.
     *
     * @return string
     */
    public function getRace(): string
    {
        return $this->race;
    }

    /**
     * Set the value of Biotime.
     *
     * @param \DateTime biotime
     *
     * @return self
     */
    public function setBiotime(\DateTime $biotime)
    {
        $this->biotime = $biotime;

        return $this;
    }

    /**
     * Get the value of Biotime.
     *
     * @return \DateTime
     */
    public function getBiotime(): \DateTime
    {
        return $this->biotime;
    }

    /**
     * Set the value of Amountouttoday.
     *
     * @param int amountouttoday
     *
     * @return self
     */
    public function setAmountouttoday($amountouttoday)
    {
        $this->amountouttoday = $amountouttoday;

        return $this;
    }

    /**
     * Get the value of Amountouttoday.
     *
     * @return int
     */
    public function getAmountouttoday(): int
    {
        return $this->amountouttoday;
    }

    /**
     * Set the value of Pk.
     *
     * @param bool pk
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
     *
     * @return bool
     */
    public function getPk(): bool
    {
        return $this->pk;
    }

    /**
     * Set the value of Dragonage.
     *
     * @param int dragonage
     *
     * @return self
     */
    public function setDragonage($dragonage)
    {
        $this->dragonage = $dragonage;

        return $this;
    }

    /**
     * Get the value of Dragonage.
     *
     * @return int
     */
    public function getDragonage(): int
    {
        return $this->dragonage;
    }

    /**
     * Set the value of Bestdragonage.
     *
     * @param int bestdragonage
     *
     * @return self
     */
    public function setBestdragonage($bestdragonage)
    {
        $this->bestdragonage = $bestdragonage;

        return $this;
    }

    /**
     * Get the value of Bestdragonage.
     *
     * @return int
     */
    public function getBestdragonage(): int
    {
        return $this->bestdragonage;
    }

    /**
     * Set the value of Ctitle.
     *
     * @param string ctitle
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
     *
     * @return string
     */
    public function getCtitle(): string
    {
        return $this->ctitle;
    }

    /**
     * Set the value of Slaydragon.
     *
     * @param bool slaydragon
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
     *
     * @return bool
     */
    public function getSlaydragon(): bool
    {
        return $this->slaydragon;
    }

    /**
     * Set the value of Fedmount.
     *
     * @param bool fedmount
     *
     * @return self
     */
    public function setFedmount($fedmount)
    {
        $this->fedmount = $fedmount;

        return $this;
    }

    /**
     * Get the value of Fedmount.
     *
     * @return bool
     */
    public function getFedmount(): bool
    {
        return $this->fedmount;
    }

    /**
     * Set the value of Clanid.
     *
     * @param int clanid
     *
     * @return self
     */
    public function setClanid($clanid)
    {
        $this->clanid = $clanid;

        return $this;
    }

    /**
     * Get the value of Clanid.
     *
     * @return int
     */
    public function getClanid(): int
    {
        return $this->clanid;
    }

    /**
     * Set the value of Clanrank.
     *
     * @param bool clanrank
     *
     * @return self
     */
    public function setClanrank($clanrank)
    {
        $this->clanrank = $clanrank;

        return $this;
    }

    /**
     * Get the value of Clanrank.
     *
     * @return bool
     */
    public function getClanrank(): bool
    {
        return $this->clanrank;
    }

    /**
     * Set the value of Clanjoindate.
     *
     * @param \DateTime clanjoindate
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
     *
     * @return \DateTime
     */
    public function getClanjoindate(): \DateTime
    {
        return $this->clanjoindate;
    }

    /**
     * Set the value of Chatloc.
     *
     * @param string chatloc
     *
     * @return self
     */
    public function setChatloc($chatloc)
    {
        $this->chatloc = $chatloc;

        return $this;
    }

    /**
     * Get the value of Chatloc.
     *
     * @return string
     */
    public function getChatloc(): string
    {
        return $this->chatloc;
    }
}
