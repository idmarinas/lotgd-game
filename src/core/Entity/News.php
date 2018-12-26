<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * News.
 *
 * @ORM\Table(name="news",
 *      indexes={
 *          @ORM\Index(name="accountid", columns={"accountid"}),
 *          @ORM\Index(name="newsdate", columns={"newsdate"})
 *      }
 * )
 * @ORM\Entity
 */
class News
{
    /**
     * @var int
     *
     * @ORM\Column(name="newsid", type="integer", nullable=false, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $newsid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="newsdate", type="date", nullable=false, options={"default":"0000-00-00"})
     */
    private $newsdate = '0000-00-00';

    /**
     * @var string
     *
     * @ORM\Column(name="newstext", type="text", length=65535, nullable=false)
     */
    private $newstext;

    /**
     * @var int
     *
     * @ORM\Column(name="accountid", type="integer", nullable=false, options={"unsigned":true})
     */
    private $accountid = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="arguments", type="text", length=65535, nullable=false)
     */
    private $arguments;

    /**
     * @var string
     *
     * @ORM\Column(name="tlschema", type="string", length=255, nullable=false, options={"default":"news"})
     */
    private $tlschema = 'news';

    /**
     * Set the value of Newsid.
     *
     * @param int newsid
     *
     * @return self
     */
    public function setNewsid($newsid)
    {
        $this->newsid = $newsid;

        return $this;
    }

    /**
     * Get the value of Newsid.
     *
     * @return int
     */
    public function getNewsid(): int
    {
        return $this->newsid;
    }

    /**
     * Set the value of Newsdate.
     *
     * @param \DateTime newsdate
     *
     * @return self
     */
    public function setNewsdate($newsdate)
    {
        if (! $newsdate instanceof \DateTimeInterface)
        {
            $this->newsdate = new \DateTime($newsdate);
        }

        return $this;
    }

    /**
     * Get the value of Newsdate.
     *
     * @return \DateTime
     */
    public function getNewsdate(): \DateTime
    {
        return $this->newsdate;
    }

    /**
     * Set the value of Newstext.
     *
     * @param string newstext
     *
     * @return self
     */
    public function setNewstext($newstext)
    {
        $this->newstext = $newstext;

        return $this;
    }

    /**
     * Get the value of Newstext.
     *
     * @return string
     */
    public function getNewstext(): string
    {
        return $this->newstext;
    }

    /**
     * Set the value of Accountid.
     *
     * @param int accountid
     *
     * @return self
     */
    public function setAccountid($accountid)
    {
        $this->accountid = $accountid;

        return $this;
    }

    /**
     * Get the value of Accountid.
     *
     * @return int
     */
    public function getAccountid(): int
    {
        return $this->accountid;
    }

    /**
     * Set the value of Arguments.
     *
     * @param string arguments
     *
     * @return self
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Get the value of Arguments.
     *
     * @return string
     */
    public function getArguments(): string
    {
        return $this->arguments;
    }

    /**
     * Set the value of Tlschema.
     *
     * @param string tlschema
     *
     * @return self
     */
    public function setTlschema($tlschema)
    {
        $this->tlschema = $tlschema;

        return $this;
    }

    /**
     * Get the value of Tlschema.
     *
     * @return string
     */
    public function getTlschema(): string
    {
        return $this->tlschema;
    }
}
