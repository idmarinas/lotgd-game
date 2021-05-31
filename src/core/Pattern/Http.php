<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 4.5.0
 */

namespace Lotgd\Core\Pattern;

use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response;

@trigger_error(Http::class . ' is deprecated, if possible use Dependency Injection.', E_USER_DEPRECATED);

/**
 * @deprecated 5.0.0 use Dependency Injection when you can, and LotgdKernel::get(ServiceName) when not can use Dependency Injection.
 */
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
            $this->lotgdHttpRequest = $this->getService(Request::class);
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
            $this->lotgdHttpResponse = $this->getService(Response::class);
        }

        return $this->lotgdHttpResponse;
    }
}
