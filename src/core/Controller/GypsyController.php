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
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Lotgd\Core\Tool\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class GypsyController extends AbstractController
{
    private $dispatcher;
    private $navigation;
    private $log;
    private $dateTime;

    public function __construct(EventDispatcherInterface $eventDispatcher, Navigation $navigation, Log $log, DateTime $dateTime)
    {
        $this->navigation = $navigation;
        $this->dispatcher = $eventDispatcher;
        $this->log        = $log;
        $this->dateTime   = $dateTime;
    }

    public function pay(array $params): Response
    {
        global $session;

        if ($session['user']['gold'] >= $params['cost'])
        {
            $session['user']['gold'] -= $params['cost'];

            $this->log->debug("spent {$params['cost']} gold to speak to the dead");

            $this->navigation->addNavAllow('gypsy.php?op=talk');

            return $this->redirect('gypsy.php?op=talk');
        }
    }

    public function talk(array $params): Response
    {
        $params['tpl'] = 'talk';

        return $this->renderGypsy($params);
    }

    public function index(array $params): Response
    {
        $params['tpl'] = 'default';

        $this->dateTime->checkDay();

        $this->dispatcher->dispatch(new GenericEvent(), Events::PAGE_GYPSY);

        return $this->renderGypsy($params);
    }

    private function renderGypsy(array $params): Response
    {
        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_GYPSY_POST);
        $params = $args->getArguments();

        return $this->render('page/gypsy.html.twig', $params);
    }
}
