<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.4.0
 */

namespace Lotgd\Core\Controller\Pattern;

use Symfony\Component\HttpFoundation\Response;

/** @deprecated 7.0.0 use Lotgd\Core\Pattern\LotgdControllerTrait */
trait RenderBlockTrait
{
    /**
     * Renders a view block.
     */
    protected function renderBlock(string $view, string $block, array $parameters = [], ?Response $response = null): Response
    {
        $content = $this->container->get('twig')->load($view)->renderBlock($block, $parameters);

        if (null === $response)
        {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }
}
