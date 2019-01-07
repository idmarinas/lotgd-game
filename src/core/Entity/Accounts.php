<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Accounts.
 *
 * @ORM\Table(name="accounts",
 *     indexes={
 *         @ORM\Index(name="login", columns={"login"}),
 *         @ORM\Index(name="laston", columns={"laston"}),
 *         @ORM\Index(name="emailaddress", columns={"emailaddress"}),
 *         @ORM\Index(name="locked", columns={"locked", "loggedin", "laston"}),
 *         @ORM\Index(name="referer", columns={"referer"}),
 *         @ORM\Index(name="uniqueid", columns={"uniqueid"}),
 *         @ORM\Index(name="emailvalidation", columns={"emailvalidation"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Lotgd\Core\EntityRepository\AccountsRepository")
 */
class Accounts
{
    /**
     * @var int
     *
     * @ORM\Column(name="acctid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $acctid;

    /**
     * @var int
     *
     * @ORM\OneToOne(targetEntity="Characters")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $character;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="laston", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $laston;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=32, nullable=false)
     */
    private $password;

    /**
     * @var bool
     *
     * @ORM\Column(name="loggedin", type="boolean", nullable=false, options={"default": 0})
     */
    private $loggedin = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="superuser", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $superuser = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", unique=true, length=50, nullable=false)
     */
    private $login;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastmotd", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $lastmotd;

    /**
     * @var bool
     *
     * @ORM\Column(name="locked", type="boolean", nullable=false, options={"default": 0})
     */
    private $locked = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="lastip", type="string", length=40, nullable=false)
     */
    private $lastip = '';

    /**
     * @var string
     *
     * @ORM\Column(name="uniqueid", type="string", length=32, nullable=true)
     */
    private $uniqueid;

    /**
     * @var bool
     *
     * @ORM\Column(name="boughtroomtoday", type="boolean", nullable=false, options={"default": 0})
     */
    private $boughtroomtoday = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="emailaddress", type="string", length=128, nullable=false)
     */
    private $emailaddress = '';

    /**
     * @var string
     *
     * @ORM\Column(name="replaceemail", type="string", length=128, nullable=false)
     */
    private $replaceemail = '';

    /**
     * @var string
     *
     * @ORM\Column(name="emailvalidation", type="string", length=32, nullable=false)
     */
    private $emailvalidation = '';

    /**
     * @var string
     *
     * @ORM\Column(name="forgottenpassword", type="string", length=32, nullable=false)
     */
    private $forgottenpassword = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="sentnotice", type="boolean", nullable=false, options={"default": 0})
     */
    private $sentnotice = false;

    /**
     * @var string
     *
     * @ORM\Column(name="prefs", type="array")
     */
    private $prefs;

    /**
     * @var int
     *
     * @ORM\Column(name="transferredtoday", type="smallint", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $transferredtoday = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="recentcomments", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $recentcomments;

    /**
     * @var int
     *
     * @ORM\Column(name="donation", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $donation = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="donationspent", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $donationspent = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="donationconfig", type="array")
     */
    private $donationconfig;

    /**
     * @var int
     *
     * @ORM\Column(name="referer", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $referer = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="refererawarded", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $refererawarded = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="banoverride", type="boolean", nullable=true, options={"default": 0})
     */
    private $banoverride = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="translatorlanguages", type="string", length=128, nullable=false, options={"default": "en"})
     */
    private $translatorlanguages = 'en';

    /**
     * @var int
     *
     * @ORM\Column(name="amountouttoday", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $amountouttoday = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="beta", type="boolean", nullable=false, options={"default": 0})
     */
    private $beta = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="regdate", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $regdate;

    /**
     * Configure same default values.
     */
    public function __construct()
    {
        $this->laston = new \DateTime('0000-00-00 00:00:00');
        $this->lastmotd = new \DateTime('0000-00-00 00:00:00');
        $this->recentcomments = new \DateTime('0000-00-00 00:00:00');
        $this->regdate = new \DateTime('0000-00-00 00:00:00');
    }

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
     * Set the value of Character.
     *
     * @param int character
     *
     * @return self
     */
    public function setCharacter($character)
    {
        $this->character = $character;

        return $this;
    }

    /**
     * Get the value of Character.
     *
     * @return int
     */
    public function getCharacter()
    {
        return $this->character;
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
     * Set the value of Password.
     *
     * @param string password
     *
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of Password.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the value of Loggedin.
     *
     * @param bool loggedin
     *
     * @return self
     */
    public function setLoggedin($loggedin)
    {
        $this->loggedin = $loggedin;

        return $this;
    }

    /**
     * Get the value of Loggedin.
     *
     * @return bool
     */
    public function getLoggedin(): bool
    {
        return $this->loggedin;
    }

    /**
     * Set the value of Superuser.
     *
     * @param int superuser
     *
     * @return self
     */
    public function setSuperuser($superuser)
    {
        $this->superuser = $superuser;

        return $this;
    }

    /**
     * Get the value of Superuser.
     *
     * @return int
     */
    public function getSuperuser(): int
    {
        return $this->superuser;
    }

    /**
     * Set the value of Login.
     *
     * @param string login
     *
     * @return self
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get the value of Login.
     *
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
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
     * Set the value of Locked.
     *
     * @param bool locked
     *
     * @return self
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get the value of Locked.
     *
     * @return bool
     */
    public function getLocked(): bool
    {
        return $this->locked;
    }

    /**
     * Set the value of Lastip.
     *
     * @param string lastip
     *
     * @return self
     */
    public function setLastip($lastip)
    {
        $this->lastip = $lastip;

        return $this;
    }

    /**
     * Get the value of Lastip.
     *
     * @return string
     */
    public function getLastip(): string
    {
        return $this->lastip;
    }

    /**
     * Set the value of Uniqueid.
     *
     * @param string uniqueid
     *
     * @return self
     */
    public function setUniqueid($uniqueid)
    {
        $this->uniqueid = $uniqueid;

        return $this;
    }

    /**
     * Get the value of Uniqueid.
     *
     * @return string
     */
    public function getUniqueid(): string
    {
        return $this->uniqueid;
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
     * Set the value of Emailaddress.
     *
     * @param string emailaddress
     *
     * @return self
     */
    public function setEmailaddress($emailaddress)
    {
        $this->emailaddress = $emailaddress;

        return $this;
    }

    /**
     * Get the value of Emailaddress.
     *
     * @return string
     */
    public function getEmailaddress(): string
    {
        return $this->emailaddress;
    }

    /**
     * Set the value of Replaceemail.
     *
     * @param string replaceemail
     *
     * @return self
     */
    public function setReplaceemail($replaceemail)
    {
        $this->replaceemail = $replaceemail;

        return $this;
    }

    /**
     * Get the value of Replaceemail.
     *
     * @return string
     */
    public function getReplaceemail(): string
    {
        return $this->replaceemail;
    }

    /**
     * Set the value of Emailvalidation.
     *
     * @param string emailvalidation
     *
     * @return self
     */
    public function setEmailvalidation($emailvalidation)
    {
        $this->emailvalidation = $emailvalidation;

        return $this;
    }

    /**
     * Get the value of Emailvalidation.
     *
     * @return string
     */
    public function getEmailvalidation(): string
    {
        return $this->emailvalidation;
    }

    /**
     * Set the value of Forgottenpassword.
     *
     * @param string forgottenpassword
     *
     * @return self
     */
    public function setForgottenpassword($forgottenpassword)
    {
        $this->forgottenpassword = $forgottenpassword;

        return $this;
    }

    /**
     * Get the value of Forgottenpassword.
     *
     * @return string
     */
    public function getForgottenpassword(): string
    {
        return $this->forgottenpassword;
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
     * Set the value of Prefs.
     *
     * @param string prefs
     *
     * @return self
     */
    public function setPrefs($prefs)
    {
        $this->prefs = $prefs;

        return $this;
    }

    /**
     * Get the value of Prefs.
     *
     * @return array
     */
    public function getPrefs()
    {
        return $this->prefs;
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
     * Set the value of Donation.
     *
     * @param int donation
     *
     * @return self
     */
    public function setDonation($donation)
    {
        $this->donation = $donation;

        return $this;
    }

    /**
     * Get the value of Donation.
     *
     * @return int
     */
    public function getDonation(): int
    {
        return $this->donation;
    }

    /**
     * Set the value of Donationspent.
     *
     * @param int donationspent
     *
     * @return self
     */
    public function setDonationspent($donationspent)
    {
        $this->donationspent = $donationspent;

        return $this;
    }

    /**
     * Get the value of Donationspent.
     *
     * @return int
     */
    public function getDonationspent(): int
    {
        return $this->donationspent;
    }

    /**
     * Set the value of Donationconfig.
     *
     * @param string donationconfig
     *
     * @return self
     */
    public function setDonationconfig($donationconfig)
    {
        $this->donationconfig = $donationconfig;

        return $this;
    }

    /**
     * Get the value of Donationconfig.
     *
     * @return string
     */
    public function getDonationconfig()
    {
        return $this->donationconfig;
    }

    /**
     * Set the value of Referer.
     *
     * @param int referer
     *
     * @return self
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;

        return $this;
    }

    /**
     * Get the value of Referer.
     *
     * @return int
     */
    public function getReferer(): int
    {
        return $this->referer;
    }

    /**
     * Set the value of Refererawarded.
     *
     * @param int refererawarded
     *
     * @return self
     */
    public function setRefererawarded($refererawarded)
    {
        $this->refererawarded = $refererawarded;

        return $this;
    }

    /**
     * Get the value of Refererawarded.
     *
     * @return int
     */
    public function getRefererawarded(): int
    {
        return $this->refererawarded;
    }

    /**
     * Set the value of Banoverride.
     *
     * @param bool banoverride
     *
     * @return self
     */
    public function setBanoverride($banoverride)
    {
        $this->banoverride = $banoverride;

        return $this;
    }

    /**
     * Get the value of Banoverride.
     *
     * @return bool
     */
    public function getBanoverride(): bool
    {
        return $this->banoverride;
    }

    /**
     * Set the value of Translatorlanguages.
     *
     * @param string translatorlanguages
     *
     * @return self
     */
    public function setTranslatorlanguages($translatorlanguages)
    {
        $this->translatorlanguages = $translatorlanguages;

        return $this;
    }

    /**
     * Get the value of Translatorlanguages.
     *
     * @return string
     */
    public function getTranslatorlanguages(): string
    {
        return $this->translatorlanguages;
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
     * Set the value of Beta.
     *
     * @param bool beta
     *
     * @return self
     */
    public function setBeta($beta)
    {
        $this->beta = $beta;

        return $this;
    }

    /**
     * Get the value of Beta.
     *
     * @return bool
     */
    public function getBeta(): bool
    {
        return $this->beta;
    }

    /**
     * Set the value of Regdate.
     *
     * @param \DateTime regdate
     *
     * @return self
     */
    public function setRegdate(\DateTime $regdate)
    {
        $this->regdate = $regdate;

        if (! $regdate instanceof \DateTimeInterface)
        {
            $this->regdate = new \DateTime($regdate);
        }

        return $this;
    }

    /**
     * Get the value of Regdate.
     *
     * @return \DateTime
     */
    public function getRegdate(): \DateTime
    {
        return $this->regdate;
    }
}
