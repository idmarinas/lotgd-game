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

use Lotgd\Core\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Commentary.
 *
 * @ORM\Table(name="commentary",
 *     indexes={
 *         @ORM\Index(name="section", columns={"section"}),
 *         @ORM\Index(name="postdate", columns={"postdate"}),
 *         @ORM\Index(name="hidden", columns={"hidden"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Lotgd\Core\EntityRepository\CommentaryRepository")
 */
class Commentary implements EntityInterface
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    private $section;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    private $command = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1000, nullable=false)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $postdate;

    /**
     * @var string
     *
     * @ORM\Column(type="array", nullable=false)
     */
    private $extra;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $author = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $authorName = '';

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $clanId = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $clanRank = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $clanName = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $clanNameShort = '';

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default": 0})
     */
    private $hidden = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=500, nullable=false)
     */
    private $hiddenComment = '';

    /**
     * @var string
     *
     * @ORM\Column(type="integer", nullable=false, options={"default": 0, "unsigned": true})
     */
    private $hiddenBy = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $hiddenByName = '';

    public function __construct()
    {
        $this->postdate = new \DateTime('now');
    }

    /**
     * Set the value of id.
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
     * Get the value of id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of Section.
     *
     * @param string section
     *
     * @return self
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get the value of Section.
     *
     * @return string
     */
    public function getSection(): string
    {
        return $this->section;
    }

    /**
     * Set the value of Author.
     *
     * @param int author
     *
     * @return self
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the value of Author.
     *
     * @return int
     */
    public function getAuthor(): int
    {
        return $this->author;
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
     * Set the value of Postdate.
     *
     * @param \DateTime postdate
     *
     * @return self
     */
    public function setPostdate(\DateTime $postdate)
    {
        $this->postdate = $postdate;

        return $this;
    }

    /**
     * Get the value of Postdate.
     *
     * @return \DateTime
     */
    public function getPostdate(): \DateTime
    {
        return $this->postdate;
    }

    /**
     * Set the value of Extra.
     *
     * @param array extra
     *
     * @return self
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Get the value of Extra.
     *
     * @return array
     */
    public function getExtra(): array
    {
        return (array) $this->extra;
    }

    /**
     * Set the value of AuthorName.
     *
     * @param string authorName
     *
     * @return self
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;

        return $this;
    }

    /**
     * Get the value of AuthorName.
     *
     * @return string
     */
    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    /**
     * Get the value of hidden.
     *
     * @return bool
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Set the value of hidden.
     *
     * @param bool $hidden
     *
     * @return self
     */
    public function setHidden(bool $hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Get the value of hiddenComment.
     *
     * @return string
     */
    public function getHiddenComment()
    {
        return $this->hiddenComment;
    }

    /**
     * Set the value of hiddenComment.
     *
     * @param string $hiddenComment
     *
     * @return self
     */
    public function setHiddenComment(string $hiddenComment)
    {
        $this->hiddenComment = $hiddenComment;

        return $this;
    }

    /**
     * Get the value of hiddenBy.
     *
     * @return string
     */
    public function getHiddenBy()
    {
        return $this->hiddenBy;
    }

    /**
     * Set the value of hiddenBy.
     *
     * @param string $hiddenBy
     *
     * @return self
     */
    public function setHiddenBy(string $hiddenBy)
    {
        $this->hiddenBy = $hiddenBy;

        return $this;
    }

    /**
     * Get the value of hiddenByName.
     *
     * @return string
     */
    public function getHiddenByName()
    {
        return $this->hiddenByName;
    }

    /**
     * Set the value of hiddenByName.
     *
     * @param string $hiddenByName
     *
     * @return self
     */
    public function setHiddenByName(string $hiddenByName)
    {
        $this->hiddenByName = $hiddenByName;

        return $this;
    }

    /**
     * Get the value of clanId.
     *
     * @return int
     */
    public function getClanId()
    {
        return $this->clanId;
    }

    /**
     * Set the value of clanId.
     *
     * @param int $clanId
     *
     * @return self
     */
    public function setClanId(int $clanId)
    {
        $this->clanId = $clanId;

        return $this;
    }

    /**
     * Get the value of clanRank.
     *
     * @return int
     */
    public function getClanRank()
    {
        return $this->clanRank;
    }

    /**
     * Set the value of clanRank.
     *
     * @param int $clanRank
     *
     * @return self
     */
    public function setClanRank(int $clanRank)
    {
        $this->clanRank = $clanRank;

        return $this;
    }

    /**
     * Get the value of clanName.
     *
     * @return string
     */
    public function getClanName()
    {
        return $this->clanName;
    }

    /**
     * Set the value of clanName.
     *
     * @param string $clanName
     *
     * @return self
     */
    public function setClanName(string $clanName)
    {
        $this->clanName = $clanName;

        return $this;
    }

    /**
     * Get the value of clanNameShort.
     *
     * @return string
     */
    public function getClanNameShort()
    {
        return $this->clanNameShort;
    }

    /**
     * Set the value of clanNameShort.
     *
     * @param string $clanNameShort
     *
     * @return self
     */
    public function setClanNameShort(string $clanNameShort)
    {
        $this->clanNameShort = $clanNameShort;

        return $this;
    }

    /**
     * Get the value of command
     *
     * @return  string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set the value of command
     *
     * @param  string  $command
     *
     * @return  self
     */
    public function setCommand(string $command)
    {
        $this->command = $command;

        return $this;
    }
}
