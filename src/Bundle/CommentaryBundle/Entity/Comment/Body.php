<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CommentaryBundle\Entity\Comment;

trait Body
{
    /**
     * Unproccessed comment (only apply filters for safety).
     *
     * @ORM\Column(type="text", length=65535)
     */
    private $rawBody = '';

    /**
     * Uncensored comment.
     *
     * @ORM\Column(type="text", length=65535)
     */
    private $uncesoredBody = '';

    /**
     * Censored words in comment.
     *
     * @ORM\Column(type="json")
     */
    private $censoredWords = [];

    public function getRawBody(): ?string
    {
        return $this->rawBody;
    }

    /**
     * @param string|null $rawBody
     */
    public function setRawBody($rawBody): self
    {
        $this->rawBody = $rawBody;

        return $this;
    }

    public function getUncesoredBody(): ?string
    {
        return $this->uncesoredBody;
    }

    public function setUncesoredBody(?string $uncesoredBody): self
    {
        $this->uncesoredBody = $uncesoredBody;

        return $this;
    }

    public function getCensoredWords(): ?array
    {
        return $this->censoredWords;
    }

    public function setCensoredWords(?array $censoredWords): self
    {
        $this->censoredWords = $censoredWords;

        return $this;
    }
}
