<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.5.0
 */

namespace Lotgd\Core\Pattern;

use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response;

trait Http
{
    protected $lotgdHttpRequest;
    protected $lotgdHttpResponse;

    /**
     * Get Request instance.
     *
     * @return object|null
     */
    public function getHttpRequest()
    {
        if ( ! $this->lotgdHttpRequest instanceof Request)
        {
            $this->lotgdHttpRequest = $this->getContainer(Request::class);
        }

        return $this->lotgdHttpRequest;
    }

    /**
     * Get Response instance.
     *
     * @return object|null
     */
    public function getHttpResponse()
    {
        if ( ! $this->lotgdHttpResponse instanceof Response)
        {
            $this->lotgdHttpResponse = $this->getContainer(Response::class);
        }

        return $this->lotgdHttpResponse;
    }
}
