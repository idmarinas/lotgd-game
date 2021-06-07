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

require_once 'lib/datetime.php';

use Lotgd\Core\Events;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AccountController extends AbstractController
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    public function index(): Response
    {
        global $session;

        $user = $session['user'];

        $dragonpointssummary = [];

        if ($user['dragonkills'] > 0)
        {
            $dragonpointssummary = \array_count_values($user['dragonpoints']);
        }

        //-- Add more statistics using templates
        $args = new GenericEvent(null, ['templates' => []]);
        $this->dispatcher->dispatch($args, Events::PAGE_ACCOUNTS_STATS);
        $tpl = modulehook('accountstats', $args->getArguments());

        $params = [
            'dragonpoints' => $dragonpointssummary,
            'templates'    => $tpl['templates'],
        ];

        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_ACCOUNTS_POST);
        $params = modulehook('page-account-tpl-params', $args->getArguments());

        return $this->render('page/account.html.twig', $params);
    }
}
