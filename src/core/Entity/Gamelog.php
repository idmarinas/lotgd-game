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
 * Gamelog.
 *
 * @ORM\Table(name="gamelog",
 *     indexes={
 *         @ORM\Index(name="date", columns={"category", "date"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\GamelogRepository")
 */
class Gamelog
{
    /**
     * @var int
     *
     * @ORM\Column(name="logid", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $logid;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=false)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=50, nullable=false)
     */
    private $category;

    /**
     * @var bool
     *
     * @ORM\Column(name="filed", type="boolean", nullable=false)
     */
    private $filed = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $date = '0000-00-00 00:00:00';

    /**
     * @var int
     *
     * @ORM\Column(name="who", type="integer", nullable=false, options={"unsigned": true})
     */
    private $who = '0';

    /**
     * Set the value of Logid.
     *
     * @param int $logid
     *
     * @return self
     */
    public function setLogid($logid)
    {
        $this->logid = $logid;

        return $this;
    }

    /**
     * Get the value of Logid.
     */
    public function getLogid(): int
    {
        return $this->logid;
    }

    /**
     * Set the value of Message.
     *
     * @param string $message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the value of Message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Set the value of Category.
     *
     * @param string $category
     *
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of Category.
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Set the value of Filed.
     *
     * @param bool $filed
     *
     * @return self
     */
    public function setFiled($filed)
    {
        $this->filed = $filed;

        return $this;
    }

    /**
     * Get the value of Filed.
     */
    public function getFiled(): bool
    {
        return $this->filed;
    }

    /**
     * Set the value of Date.
     *
     * @param \DateTime|\DateTimeImmutable $date
     *
     * @return self
     */
    public function setDate(\DateTimeInterface $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of Date.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Set the value of Who.
     *
     * @param int $who
     *
     * @return self
     */
    public function setWho($who)
    {
        $this->who = $who;

        return $this;
    }

    /**
     * Get the value of Who.
     */
    public function getWho(): int
    {
        return $this->who;
    }
}
