<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.2.0
 */

namespace Lotgd\Core\Event;

use Symfony\Contracts\EventDispatcher\Event;

class Commentary extends Event
{
    // Post comment. Old: postcomment
    public const COMMENT_POST = 'lotgd.core.comentary.comment.post';

    // Commands. Old: commentary-command
    public const COMMANDS = 'lotgd.core.comentary.commands';

    // Comment. Old: commentary-comment
    public const COMMENT = 'lotgd.core.comentary.comment';

    // Moderate sections. Old: moderate-comment-sections
    public const MODERATE_SECTIONS = 'lotgd.core.comentary.moderate.sections';

    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
