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

namespace Lotgd\Core\Controller;

use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Http\Response as HttpResponse;
use Lotgd\Core\Navigation\Navigation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ShadesController extends AbstractController
{
    private $dispatcher;
    private $response;
    private $navigation;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        HttpResponse $response,
        Navigation $navigation
    ) {
        $this->dispatcher = $eventDispatcher;
        $this->response   = $response;
        $this->navigation = $navigation;
    }

    public function index(Request $request): Response
    {
        // Don't hook on to this text for your standard modules please, use "shades" instead.
        // This hook is specifically to allow modules that do other shades to create ambience.
        $args = new GenericEvent(null, ['textDomain' => 'page_shades', 'textDomainNavigation' => 'navigation_shades']);
        $this->dispatcher->dispatch($args, Events::PAGE_SHADES_PRE);
        $result               = $args->getArguments();
        $textDomain           = $result['textDomain'];
        $textDomainNavigation = $result['textDomainNavigation'];
        unset($result);

        $params = [
            'textDomain'           => $textDomain,
            'includeTemplatesPre'  => [], //-- Templates that are in top of content (but below of title)
            'includeTemplatesPost' => [], //-- Templates that are in bottom of content
        ];

        $this->response->pageTitle('title', [], $textDomain);

        //-- Change text domain for navigation
        $this->navigation->setTextDomain($textDomainNavigation);

        $this->navigation->addHeader('category.logout');
        $this->navigation->addNav('nav.logout', 'login.php?op=logout');

        $this->navigation->addHeader('category.places');
        $this->navigation->addNav('nav.graveyard', 'graveyard.php');
        $this->navigation->addNav('nav.news', 'news.php');

        // the mute module blocks players from speaking until they
        // read the FAQs, and if they first try to speak when dead
        // there is no way for them to unmute themselves without this link.
        $this->navigation->addHeader('category.other');

        //-- Superuser menu
        $this->navigation->superuser();

        $request->attributes->set('params', $params);

        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_SHADES_POST);
        $params = $args->getArguments();

        //-- Restore text domain for navigation
        $this->navigation->setTextDomain();

        return $this->render('page/shades.html.twig', $params);
    }
}
