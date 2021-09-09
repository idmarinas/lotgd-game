<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Structure of table "user" in data base.
 *
 * This table store users, only data related to user.
 *
 * @ORM\Table(
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
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\UserRepository")
 */
class User implements UserInterface
{
    use User\Avatar;
    use User\Ban;
    use User\Donation;
    use User\Referer;
    use User\Security;

    /**
     * @var int
     *
     * @ORM\Column(name="acctid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $acctid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="laston", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $laston;

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
    private $superuser = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", unique=true, length=50, nullable=false)
     */
    private $login;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

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
     * @ORM\Column(name="uniqueid", type="string", length=32, nullable=false)
     */
    private $uniqueid = '';

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
     * @ORM\Column(name="emailvalidation", type="string", length=32, nullable=true)
     */
    private $emailvalidation = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="sentnotice", type="boolean", nullable=false, options={"default": 0})
     */
    private $sentnotice = false;

    /**
     * @var array
     *
     * @ORM\Column(name="prefs", type="array")
     */
    private $prefs = [];

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
     * @ORM\Column(name="amountouttoday", type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $amountouttoday = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="regdate", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $regdate;

    /**
     * Configure some default values.
     */
    public function __construct()
    {
        $this->laston         = new \DateTime('0000-00-00 00:00:00');
        $this->lastmotd       = new \DateTime('0000-00-00 00:00:00');
        $this->recentcomments = new \DateTime('0000-00-00 00:00:00');
        $this->regdate        = new \DateTime('0000-00-00 00:00:00');
    }

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
     * Set the value of Laston.
     *
     * @param \DateTime|\DateTimeImmutable $laston
     *
     * @return self
     */
    public function setLaston(\DateTimeInterface $laston)
    {
        $this->laston = $laston;

        return $this;
    }

    /**
     * Get the value of Laston.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getLaston(): \DateTimeInterface
    {
        return $this->laston;
    }

    /**
     * Set the value of Loggedin.
     *
     * @param bool $loggedin
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
     */
    public function getLoggedin(): bool
    {
        return $this->loggedin;
    }

    /**
     * Set the value of Superuser.
     *
     * @param int $superuser
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
     */
    public function getSuperuser(): int
    {
        return $this->superuser;
    }

    /**
     * Set the value of Login.
     *
     * @param string $login
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
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * Get the value of Login.
     */
    public function getUsername(): string
    {
        return $this->getLogin();
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $merged = $this->roles;
        // guarantee every user at least has ROLE_USER
        $merged[] = 'ROLE_USER';

        return array_unique($merged);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Set the value of Lastmotd.
     *
     * @param \DateTime|\DateTimeImmutable $lastmotd
     *
     * @return self
     */
    public function setLastmotd(\DateTimeInterface $lastmotd)
    {
        $this->lastmotd = $lastmotd;

        return $this;
    }

    /**
     * Get the value of Lastmotd.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getLastmotd(): \DateTimeInterface
    {
        return $this->lastmotd;
    }

    /**
     * Set the value of Locked.
     *
     * @param bool $locked
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
     */
    public function getLocked(): bool
    {
        return $this->locked;
    }

    /**
     * Set the value of Lastip.
     *
     * @param string $lastip
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
     */
    public function getLastip(): string
    {
        return $this->lastip;
    }

    /**
     * Set the value of Uniqueid.
     *
     * @param string $uniqueid
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
     */
    public function getUniqueid(): string
    {
        return $this->uniqueid;
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
     * Set the value of Emailaddress.
     *
     * @return self
     */
    public function setEmailaddress(string $emailaddress)
    {
        $this->emailaddress = $emailaddress;

        return $this;
    }

    /**
     * Get the value of Emailaddress.
     */
    public function getEmailaddress(): string
    {
        return $this->emailaddress;
    }

    /**
     * Set the value of Replaceemail.
     *
     * @param string $replaceemail
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
     */
    public function getReplaceemail(): string
    {
        return $this->replaceemail;
    }

    /**
     * Set the value of Emailvalidation.
     *
     * @param string $emailvalidation
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
     */
    public function getEmailvalidation(): string
    {
        return $this->emailvalidation;
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
     * Set the value of Prefs.
     *
     * @param array $prefs
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
     * @param int $transferredtoday
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
     */
    public function getTransferredtoday(): int
    {
        return $this->transferredtoday;
    }

    /**
     * Set the value of Recentcomments.
     *
     * @param \DateTime|\DateTimeImmutable $recentcomments
     *
     * @return self
     */
    public function setRecentcomments(\DateTimeInterface $recentcomments)
    {
        $this->recentcomments = $recentcomments;

        return $this;
    }

    /**
     * Get the value of Recentcomments.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getRecentcomments(): \DateTimeInterface
    {
        return $this->recentcomments;
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
        $this->amountouttoday = $amountouttoday;

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
     * Set the value of Regdate.
     *
     * @param \DateTime|\DateTimeImmutable $regdate
     *
     * @return self
     */
    public function setRegdate(\DateTimeInterface $regdate)
    {
        $this->regdate = $regdate;

        return $this;
    }

    /**
     * Get the value of Regdate.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getRegdate(): \DateTimeInterface
    {
        return $this->regdate;
    }
}
