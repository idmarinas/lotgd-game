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
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class HealerController extends AbstractController
{
    private $dispatcher;
    private $log;
    private $navigation;
    private $response;

    public function __construct(EventDispatcherInterface $eventDispatcher, Log $log, Navigation $navigation, HttpResponse $response)
    {
        $this->dispatcher = $eventDispatcher;
        $this->log        = $log;
        $this->navigation = $navigation;
        $this->response   = $response;
    }

    public function index(Request $request): Response
    {
        global $session;

        // Don't hook on to this text for your standard modules please, use "healer" instead.
        // This hook is specifically to allow modules that do other healers to create ambience.
        $args = new GenericEvent(null, ['textDomain' => 'page_healer', 'textDomainNavigation' => 'navigation_healer']);
        $this->dispatcher->dispatch($args, Events::PAGE_HEALER_PRE);
        $result               = $args->getArguments();
        $textDomain           = $result['textDomain'];
        $textDomainNavigation = $result['textDomainNavigation'];
        unset($result);

        //-- Init page
        $this->response->pageTitle('title', [], $textDomain);

        //-- Calculate cost for healing
        $cost = log($session['user']['level']) * (($session['user']['maxhitpoints'] - $session['user']['hitpoints']) + 10);
        $args = new GenericEvent(null, ['alterpct' => 1.0, 'cost' => $cost]);
        $this->dispatcher->dispatch($args, Events::PAGE_HEALER_MULTIPLY);
        $result = $args->getArguments();
        $cost   = round($result['alterpct'] * $result['cost'], 0);

        $params = [
            'textDomain' => $textDomain,
            'healCost'   => $cost,
        ];

        $op     = (string) $request->query->get('op', '');
        $method = method_exists($this, $op) ? $op : 'enter';
        $return = (string) $request->query->get('return');

        //-- Change text domain for navigation
        $this->navigation->setTextDomain($textDomainNavigation);

        $this->navigation->addHeader('category.return');

        if ('' == $return)
        {
            $this->navigation->addNav('nav.return.forest', 'forest.php');
            $this->navigation->villageNav();
        }
        elseif ('village.php' == $return)
        {
            $this->navigation->villageNav();
        }
        else
        {
            $this->navigation->addNav('nav.return.return', $return);
        }

        $params['return'] = $return;

        return $this->{$method}($params, $request);
    }

    protected function buy(array $params, Request $request): Response
    {
        global $session;

        $pct     = $request->query->getInt('pct');
        $newcost = round($pct * $params['healCost'] / 100, 0);

        $params['tpl']         = 'buy';
        $params['newHealCost'] = $newcost;
        $params['canHeal']     = false;

        if ($session['user']['gold'] >= $newcost)
        {
            $diff = round(($session['user']['maxhitpoints'] - $session['user']['hitpoints']) * $pct / 100, 0);

            $params['canHeal']    = true;
            $params['healHealed'] = $diff;

            $session['user']['gold'] -= $newcost;
            $session['user']['hitpoints'] += $diff;

            $this->log->debug('spent gold on healing', false, false, 'healing', $newcost);
        }

        return $this->renderHealer($params);
    }

    protected function companion(array $params, Request $request): Response
    {
        global $session, $companions;

        $compcost = $request->query->getInt('compcost');

        $params['tpl']         = 'companion';
        $params['canHeal']     = false;
        $params['newHealCost'] = $compcost;

        if ($session['user']['gold'] >= $compcost)
        {
            $params['canHeal'] = true;

            $name = stripslashes(rawurldecode($request->query->get('name')));

            $session['user']['gold'] -= $compcost;
            $companions[$name]['hitpoints'] = $companions[$name]['maxhitpoints'];

            $params['companionName'] = $companions[$name]['name'];
            $this->log->debug("spent gold on companion healing '{$name}'", false, false, 'healing-companion', $compcost);
        }

        return $this->renderHealer($params);
    }

    protected function enter(array $params): Response
    {
        global $session;

        $params['tpl']      = 'default';
        $params['needHeal'] = false;

        if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'])
        {
            $params['needHeal'] = true;
        }
        elseif ($session['user']['hitpoints'] == $session['user']['maxhitpoints'])
        {
            $params['needHeal'] = 0;
        }

        if (false === $params['needHeal'])
        {
            $session['user']['hitpoints'] = $session['user']['maxhitpoints'];
        }

        return $this->renderHealer($params);
    }

    private function renderHealer(array $params): Response
    {
        global $session, $companions;

        if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'])
        {
            $this->navigation->addHeader('category.heal.potion');
            $this->navigation->addNav('nav.heal.complete', "healer.php?op=buy&pct=100&return={$params['return']}");

            for ($i = 90; $i > 0; $i -= 10)
            {
                $this->navigation->addNav('nav.heal.percent', "healer.php?op=buy&pct={$i}&return={$params['return']}", [
                    'params' => [
                        'percent' => $i / 100,
                        'cost'    => round($params['healCost'] * ($i / 100), 0),
                    ],
                ]);
            }
            $this->dispatcher->dispatch(new GenericEvent(), Events::PAGE_HEALER_POTION);
        }
        $this->navigation->addHeader('category.heal.companion');

        foreach ($companions as $name => $companion)
        {
            if ($companion['cannotbehealed'] ?? false)
            {
                continue;
            }

            $points = $companion['maxhitpoints'] - $companion['hitpoints'];

            if ($points > 0)
            {
                $name     = rawurlencode($name);
                $compcost = round(log($session['user']['level'] + 1) * ($points + 10) * 1.33);
                $this->navigation->addNav('nav.heal.companion', "healer.php?op=companion&name={$name}&compcost={$compcost}&return={$params['return']}", [
                    'params' => [
                        'companionName' => $companion['name'],
                        'cost'          => $compcost,
                    ],
                ]);
            }
        }

        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_HEALER_POST);
        $params = $args->getArguments();

        //-- Restore text domain for navigation
        $this->navigation->setTextDomain();

        return $this->render('page/healer.html.twig', $params);
    }
}
