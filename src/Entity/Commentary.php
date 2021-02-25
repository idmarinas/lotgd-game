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
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     */
    private $section;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20)
     */
    private $command = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1000)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1000)
     */
    private $commentRaw;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", options={"default": "0000-00-00 00:00:00"})
     */
    private $postdate;

    /**
     * @var string
     *
     * @ORM\Column(type="array")
     */
    private $extra;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default": 0, "unsigned": true})
     */
    private $author = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $authorName = '';

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default": 0, "unsigned": true})
     */
    private $clanId = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", options={"default": 0, "unsigned": true})
     */
    private $clanRank = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $clanName = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
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
     * @param int $id
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
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of Section.
     *
     * @param string $section
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
     */
    public function getSection(): string
    {
        return $this->section;
    }

    /**
     * Set the value of Author.
     *
     * @param int $author
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
     */
    public function getAuthor(): int
    {
        return $this->author;
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
     * Get the value of commentRaw.
     *
     * @return string
     */
    public function getCommentRaw()
    {
        return $this->commentRaw;
    }

    /**
     * Set the value of commentRaw.
     *
     * @return self
     */
    public function setCommentRaw(string $commentRaw)
    {
        $this->commentRaw = $commentRaw;

        return $this;
    }

    /**
     * Set the value of Postdate.
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
     */
    public function getPostdate(): \DateTime
    {
        return $this->postdate;
    }

    /**
     * Set the value of Extra.
     *
     * @param array $extra
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
     */
    public function getExtra(): array
    {
        return (array) $this->extra;
    }

    /**
     * Set the value of AuthorName.
     *
     * @param string $authorName
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
     * @return self
     */
    public function setClanNameShort(string $clanNameShort)
    {
        $this->clanNameShort = $clanNameShort;

        return $this;
    }

    /**
     * Get the value of command.
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set the value of command.
     *
     * @return self
     */
    public function setCommand(string $command)
    {
        $this->command = $command;

        return $this;
    }
}
