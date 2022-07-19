<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 5.5.0
 */

namespace Lotgd\Core\Controller;

use Lotgd\Core\Events;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Navigation\Navigation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ForestController extends AbstractController
{
    private $dispatcher;
    private $navigation;
    private $settings;

    public function __construct(EventDispatcherInterface $dispatcher, Navigation $navigation, Settings $settings)
    {
        $this->dispatcher = $dispatcher;
        $this->navigation = $navigation;
        $this->settings   = $settings;
    }

    public function index(array $params): Response
    {
        global $session;

        $params['tpl'] = 'default';

        $this->navigation->forestNav($params['translation_domain_navigation']);

        $this->dispatcher->dispatch(new GenericEvent(), Events::PAGE_FOREST_HEADER);

        if ($session['user']['level'] >= $this->settings->getSetting('maxlevel', 15) && 0 == $session['user']['seendragon'])
        {
            // Only put the green dragon link if we are a location which
            // should have a forest.   Don't even ask how we got into a forest()
            // call if we shouldn't have one.   There is at least one way via
            // a superuser link, but it shouldn't happen otherwise.. We just
            // want to make sure however.
            $isforest = 0;
            $args     = new GenericEvent();
            $this->dispatcher->dispatch($args, Events::PAGE_FOREST_VALID_FOREST_LOC);
            $vloc = $args->getArguments();

            foreach ($vloc as $i => $l)
            {
                if ($session['user']['location'] == $i)
                {
                    $isforest = 1;

                    break;
                }
            }

            if ($isforest || 0 == \count($vloc))
            {
                $this->navigation->addNav('nav.dragon', 'forest.php?op=dragon');
            }
        }

        $args = new GenericEvent();
        $this->dispatcher->dispatch($args, Events::PAGE_FOREST);

        return $this->renderForest($params);
    }

    public function dragon(array $params): Response
    {
        $params['tpl'] = 'dragon';

        return $this->renderForest($params);
    }

    private function renderForest(array $params): Response
    {
        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_FOREST_POST);
        $params = $args->getArguments();

        return $this->render('page/forest.html.twig', $params);
    }
}
