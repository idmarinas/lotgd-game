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

trait Comment
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    private $command = '';

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $section;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1000, nullable=false)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1000, nullable=false)
     */
    private $commentRaw;

    /**
     * @var array
     *
     * @ORM\Column(type="array", nullable=false)
     */
    private $extra = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $translatable = false;

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
     * Set the value of Extra.
     *
     * @return self
     */
    public function setExtra(array $extra)
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

    public function getTranslatable(): ?bool
    {
        return $this->translatable;
    }

    public function setTranslatable(bool $translatable): self
    {
        $this->translatable = $translatable;

        return $this;
    }
}
