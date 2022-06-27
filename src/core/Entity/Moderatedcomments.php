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

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Moderatedcomments.
 *
 * @ORM\Table(name="moderatedcomments")
 * @ORM\Entity
 */
class Moderatedcomments
{
    /**
     *
     * @ORM\Column(name="modid", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $modid = null;
    /**
     *
     * @ORM\Column(name="comment", type="text", length=65535)
     */
    private ?string $comment = null;

    /**
     *
     * @ORM\Column(name="moderator", type="integer", options={"unsigned"=true, "default"="0"})
     */
    private ?int $moderator = 0;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="moddate", type="datetime")
     */
    private ?\DateTimeInterface $moddate = null;

    public function __construct()
    {
        $this->moddate = new DateTime('0000-00-00 00:00:00');
    }

    /**
     * Set the value of Modid.
     *
     * @param int $modid
     *
     * @return self
     */
    public function setModid($modid)
    {
        $this->modid = $modid;

        return $this;
    }

    /**
     * Get the value of Modid.
     */
    public function getModid(): int
    {
        return $this->modid;
    }

    /**
     * Set the value of Comment.
     *
     * @param string $comment
     *
     * @return self
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the value of Comment.
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Set the value of Moderator.
     *
     * @param int $moderator
     *
     * @return self
     */
    public function setModerator($moderator)
    {
        $this->moderator = $moderator;

        return $this;
    }

    /**
     * Get the value of Moderator.
     */
    public function getModerator(): int
    {
        return $this->moderator;
    }

    /**
     * Set the value of Moddate.
     *
     * @param \DateTime|\DateTimeImmutable $moddate
     *
     * @return self
     */
    public function setModdate(DateTimeInterface $moddate)
    {
        $this->moddate = $moddate;

        return $this;
    }

    /**
     * Get the value of Moddate.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getModdate(): DateTimeInterface
    {
        return $this->moddate;
    }
}
