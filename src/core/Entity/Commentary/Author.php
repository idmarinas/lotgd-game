<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.1.0
 */

namespace Lotgd\Core\Entity\Commentary;

trait Author
{
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
}
