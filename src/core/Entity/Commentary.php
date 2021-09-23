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
 * Commentary.
 *
 * @ORM\Table(name="commentary",
 *     indexes={
 *         @ORM\Index(name="section", columns={"section"}),
 *         @ORM\Index(name="postdate", columns={"postdate"}),
 *         @ORM\Index(name="hidden", columns={"hidden"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Lotgd\Core\Repository\CommentaryRepository")
 */
class Commentary implements EntityInterface
{
    use Common\IdTrait;
    use Commentary\Author;
    use Commentary\Comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $postdate;

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
     * Set the value of Postdate.
     *
     * @param \DateTime|\DateTimeImmutable $postdate
     *
     * @return self
     */
    public function setPostdate(\DateTimeInterface $postdate)
    {
        $this->postdate = $postdate;

        return $this;
    }

    /**
     * Get the value of Postdate.
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function getPostdate(): \DateTimeInterface
    {
        return $this->postdate;
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
}
