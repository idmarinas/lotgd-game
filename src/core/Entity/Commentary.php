<?php

namespace Lotgd\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commentary.
 *
 * @ORM\Table(name="commentary",
 *      indexes={
 *          @ORM\Index(name="section", columns={"section"}),
 *          @ORM\Index(name="postdate", columns={"postdate"})
 *      }
 * )
 * @ORM\Entity
 */
class Commentary
{
    /**
     * @var int
     *
     * @ORM\Column(name="commentid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $commentid;

    /**
     * @var string
     *
     * @ORM\Column(name="section", type="string", length=20, nullable=true)
     */
    private $section;

    /**
     * @var int
     *
     * @ORM\Column(name="author", type="integer", nullable=false)
     */
    private $author = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=600, nullable=false)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="postdate", type="datetime", nullable=false)
     */
    private $postdate = '0000-00-00 00:00:00';

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", length=65535, nullable=false)
     */
    private $info;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", length=65535, nullable=false)
     */
    private $name;

    /**
     * Set the value of Commentid.
     *
     * @param int commentid
     *
     * @return self
     */
    public function setCommentid($commentid)
    {
        $this->commentid = $commentid;

        return $this;
    }

    /**
     * Get the value of Commentid.
     *
     * @return int
     */
    public function getCommentid(): int
    {
        return $this->commentid;
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
     * Set the value of Info.
     *
     * @param string info
     *
     * @return self
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get the value of Info.
     *
     * @return string
     */
    public function getInfo(): string
    {
        return $this->info;
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
}
