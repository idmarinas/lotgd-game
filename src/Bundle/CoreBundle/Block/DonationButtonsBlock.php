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

namespace Lotgd\Bundle\CoreBundle\Block;

use Lotgd\Bundle\Kernel;
use Lotgd\Bundle\UserBundle\Repository\UserRepository;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;

final class DonationButtonsBlock extends AbstractBlockService
{
    /** Symfony\Component\HttpFoundation\Request */
    protected $request;
    protected $repository;
    protected $router;
    protected $session;
    protected $security;

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $uri    = $this->request->server->get('REQUEST_URI');
        $host   = $this->request->server->get('HTTP_HOST');
        $schema = 'On' == $this->request->server->get('HTTPS') ? 'https' : 'http';
        /** @var Lotgd\Bundle\UserBundle\Entity\User */
        $user = $this->security->getUser();

        $now = new \DateTime('now');
        $now->sub(new \DateInterval('PT1H'));
        $cacheTime = 900;

        $alreadyRegisteredLogdnet = true;

        if ( ! $this->session->get('logdnet_registered', false) || ! $user || $now > $user->getLastConnection())
        {
            $cacheTime                = 0;
            $alreadyRegisteredLogdnet = false;
            $this->session->set('logdnet_registered', true);
        }

        $author['register_logdnet'] = false;

        if ($user && ! $alreadyRegisteredLogdnet)
        {
            $author['register_logdnet'] = true;
            //-- User counting, just for my own records, I don't use this in the calculation for server order.
            $author['c'] = \rawurlencode($this->repository->count([]));
            $author['v'] = \rawurlencode(Kernel::VERSION);
            $author['a'] = \rawurlencode("{$schema}://{$host}/");
        }

        $name = $user ? $user->getUserName() : 'Annonymous';

        return $this->renderResponse('@LotgdCore/block/paypal.html.twig', [
            'settings'    => $blockContext->getSettings(),
            'block'       => $blockContext->getBlock(),
            'item_number' => \htmlentities($name, ENT_COMPAT, 'UTF-8').':'.$host.'/'.$uri,
            'notify_url'  => '//'.$host.'/payment',
            'author'      => $author,
        ], $response)->setTtl($cacheTime);
    }

    public function setRequest(RequestStack $request)
    {
        $this->request = $request->getMasterRequest();
    }

    public function setRepository(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    public function setSecurity(Security $security)
    {
        $this->security = $security;
    }
}
