<?php

namespace Lotgd\Core\Entity;

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
     * @var int
     *
     * @ORM\Column(name="modid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $modid;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", length=65535, nullable=true)
     */
    private $comment;

    /**
     * @var int
     *
     * @ORM\Column(name="moderator", type="integer", nullable=false)
     */
    private $moderator = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="moddate", type="datetime", nullable=false)
     */
    private $moddate = '0000-00-00 00:00:00';

    /**
     * Set the value of Modid.
     *
     * @param int modid
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
     *
     * @return int
     */
    public function getModid(): int
    {
        return $this->modid;
    }

    /**
     * Set the value of Comment.
     *
     * @param string comment
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
     *
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Set the value of Moderator.
     *
     * @param int moderator
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
     *
     * @return int
     */
    public function getModerator(): int
    {
        return $this->moderator;
    }

    /**
     * Set the value of Moddate.
     *
     * @param \DateTime moddate
     *
     * @return self
     */
    public function setModdate(\DateTime $moddate)
    {
        $this->moddate = $moddate;

        return $this;
    }

    /**
     * Get the value of Moddate.
     *
     * @return \DateTime
     */
    public function getModdate(): \DateTime
    {
        return $this->moddate;
    }
}
