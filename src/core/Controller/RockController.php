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
use Lotgd\Core\Http\Response as HttpResponse;
use Lotgd\Core\Navigation\Navigation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RockController extends AbstractController
{
    private $dispatcher;
    private $navigation;
    private $response;

    public function __construct(EventDispatcherInterface $eventDispatcher, Navigation $navigation, HttpResponse $response)
    {
        $this->dispatcher = $eventDispatcher;
        $this->navigation = $navigation;
        $this->response   = $response;
    }

    public function index(): Response
    {
        global $session;

        // Don't hook on to this text for your standard modules please, use "rock" instead.
        // This hook is specifically to allow modules that do other rocks to create ambience.
        $args = new GenericEvent(null, ['textDomain' => 'page_rock', 'textDomainNavigation' => 'navigation_rock']);
        $this->dispatcher->dispatch($args, Events::PAGE_ROCK_PRE);
        $result               = $args->getArguments();
        $textDomain           = $result['textDomain'];
        $textDomainNavigation = $result['textDomainNavigation'];
        unset($result);

        //-- Change text domain for navigation
        $this->navigation->setTextDomain($textDomainNavigation);

        $this->navigation->villageNav();

        $params = [
            'textDomain' => $textDomain,
        ];

        $params['tpl'] = 'default';
        $title         = 'title.default';

        if ($session['user']['dragonkills'] > 0 || $session['user']['superuser'] & SU_EDIT_COMMENTS)
        {
            $params['tpl'] = 'veteran';
            $title         = 'title.veteran';
        }

        //-- Init page
        $this->response->pageTitle($title, [], $textDomain);

        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_ROCK_POST);
        $params = $args->getArguments();

        return $this->render('page/rock.html.twig', $params);
    }
}
