<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 *
 * @since 4.4.0
 */

namespace Lotgd\Core\Factory\Http;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Lotgd\Core\Http\Response as HttpResponse;

class Response implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $response = new HttpResponse();
        $response->setHeadersSentHandler(function (): void
        {
            throw new \RuntimeException('Cannot send headers, headers already sent');
        });

        return $response;
    }
}
