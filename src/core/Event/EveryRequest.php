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

class EveryRequest extends Event
{
    /**
     * Event that occurs in every request in header.
     */
    public const HEADER = 'lotgd.core.every.header';

    /**
     * Event that occurs in every request in header for authenticated user.
     */
    public const HEADER_AUTHENTICATED = 'lotgd.core.every.header';

    /**
     * Event that occurs in every request in footer.
     */
    public const FOOTER = 'lotgd.core.every.footer.authenticated';

    /**
     * Event that occurs in every request in footer for authenticated user.
     */
    public const FOOTER_AUTHENTICATED = 'lotgd.core.every.footer.authenticated';

    /**
     * Event that occurs in every request in hit.
     */
    public const HIT = 'lotgd.core.every.hit.authenticated';

    /**
     * Event that occurs in every request in hit for authenticated user.
     */
    public const HIT_AUTHENTICATED = 'lotgd.core.every.hit.authenticated';

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
