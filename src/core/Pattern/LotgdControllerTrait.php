<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 7.0.0
 */

namespace Lotgd\Core\Pattern;

use LogicException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Custom trait for LoTGD Controllers
 */
trait LotgdControllerTrait
{
    /**
     * Adds a notification to the current session for type.
     *
     * @throws \LogicException
     * @return never
     */
    protected function addNotification(string $type, $message)
    {
        if (!$this->container->has('session'))
        {
            throw new LogicException('You can not use the addNotification method if sessions are disabled. Enable them in "config/packages/framework.yaml".');
        }

        $this->container->get('session')->getBag('notifications')->add($type, $message);
    }

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
