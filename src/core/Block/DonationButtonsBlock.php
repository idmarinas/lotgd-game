<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.1.0
 */

namespace Lotgd\Core\Block;

use Lotgd\Core\Http\Request;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;

final class DonationButtonsBlock extends AbstractBlockService
{
    protected $request;

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        global $session;

        $uri  = $this->request->getServer('REQUEST_URI');
        $host = $this->request->getServer('HTTP_HOST');

        return $this->renderResponse('admin/paypal.html.twig', [
            'settings'    => $blockContext->getSettings(),
            'block'       => $blockContext->getBlock(),
            'item_number' => \htmlentities($session['user']['login'], ENT_COMPAT, 'UTF-8').':'.$host.'/'.$uri,
            'notify_url'  => '//'.$host.\dirname($uri).'/payment.php',
        ], $response);
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}
