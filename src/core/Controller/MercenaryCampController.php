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

use Lotgd\Core\Combat\Buffer;
use Lotgd\Core\Repository\CompanionsRepository;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Log;
use Lotgd\Core\Navigation\Navigation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MercenaryCampController extends AbstractController
{
    private $navigation;
    private $dispatcher;
    private $repository;
    private $log;
    private $buffs;

    public function __construct(Navigation $navigation, EventDispatcherInterface $eventDispatcher, Log $log, Buffer $buffs)
    {
        $this->navigation = $navigation;
        $this->dispatcher = $eventDispatcher;
        $this->log        = $log;
        $this->buffs = $buffs;
    }

    public function heal(array $params, Request $request): Response
    {
        global $session, $companions;

        $params['tpl'] = 'heal';

        $name = \stripslashes(\rawurldecode($request->query->get('name')));

        $pointsToHeal = $companions[$name]['maxhitpoints'] - $companions[$name]['hitpoints'];
        $costToHeal   = \round(\log($session['user']['level'] + 1) * ($pointsToHeal + 10) * 1.33);

        $params['companionHealed'] = false;
        if ($session['user']['gold'] >= $costToHeal)
        {
            $params['companionHealed'] = true;

            $companions[$name]['hitpoints'] = $companions[$name]['maxhitpoints'];
            $session['user']['companions']  = $companions;
            $session['user']['gold'] -= $costToHeal;

            $this->log->debug("spent {$costToHeal} gold on healing a companion", false, false, 'healcompanion', $costToHeal);
        }

        $params['companionName'] = $companions[$name]['name'];

        $params['companionWounds'] = $this->healNav();

        $this->navigation->addHeader('category.navigation');
        $this->navigation->addNav('nav.return', 'mercenarycamp.php?skip=1');

        return $this->renderCamp($params);
    }

    public function buy(array $params, Request $request): Response
    {
        global $session;

        $params['tpl'] = 'buy';

        $companionId = $request->query->getInt('id');

        $entity = $this->getRepository()->find($companionId);

        $params['companionHire'] = 'not.found';

        if ($entity)
        {
            $row = $this->getRepository()->extractEntity($entity);

            $row['attack']       = $row['attack']       + $row['attackperlevel']       * $session['user']['level'];
            $row['defense']      = $row['defense']      + $row['defenseperlevel']      * $session['user']['level'];
            $row['maxhitpoints'] = $row['maxhitpoints'] + $row['maxhitpointsperlevel'] * $session['user']['level'];
            $row['hitpoints']    = $row['maxhitpoints'];

            $params['companionHire'] = $this->buffs->applyCompanion($row['name'], $row);

            if ($params['companionHire'])
            {
                $session['user']['gold'] -= $row['companioncostgold'];
                $session['user']['gems'] -= $row['companioncostgems'];

                $this->log->debug("has spent {$row['companioncostgold']} gold and {$row['companioncostgems']} gems on hiring a mercenary ({$row['name']}).");
            }
        }

        $this->navigation->addHeader('category.navigation');
        $this->navigation->addNav('nav.return', 'mercenarycamp.php?skip=1');

        return $this->renderCamp($params);
    }

    public function index(array $params, Request $request): Response
    {
        global $session, $companions;

        $skip = $request->query->getInt('skip');

        $params['tpl']             = 'default';
        $params['showDescription'] = ! $skip;
        $params['companions']      = $this->getRepository()->getMercenaryList($session['user']['location'], $session['user']['dragonkills']);

        $this->navigation->addHeader('category.buynav');

        foreach ($params['companions'] as $row)
        {
            if ($row['companioncostgold'] || $row['companioncostgems'])
            {
                $link      = '';
                $navParams = [
                    'params' => [
                        'name'     => $row['name'],
                        'costGold' => $row['companioncostgold'],
                        'costGems' => $row['companioncostgems'],
                    ],
                ];

                if ($session['user']['gold'] >= $row['companioncostgold'] && $session['user']['gems'] >= $row['companioncostgems'] && ! isset($companions[$row['name']]))
                {
                    $link = "mercenarycamp.php?op=buy&id={$row['companionid']}";
                }

                $this->navigation->addNav('nav.companion.cost', $link, $navParams);
            }
            elseif ( ! isset($companions[$row['name']]))
            {
                $this->navigation->addNav($row['name'], "mercenarycamp.php?op=buy&id={$row['companionid']}", ['translate' => false]);
            }
        }

        $params['companionWounds'] = $this->healNav();

        return $this->renderCamp($params);
    }

    private function renderCamp(array $params): Response
    {
        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_MERCENARY_CAMP_POST);
        $params = modulehook('page-mercenarycamp-tpl-params', $args->getArguments());

        return $this->render('page/mercenarycamp.html.twig', $params);
    }

    private function getRepository(): CompanionsRepository
    {
        if ( ! $this->repository instanceof CompanionsRepository)
        {
            $this->repository = $this->getDoctrine()->getRepository('LotgdCore:Companions');
        }

        return $this->repository;
    }

    /**
     * Navs for heal companions.
     */
    private function healNav(): bool
    {
        global $session, $companions;

        $this->navigation->addHeader('category.companion.heal');

        $healable = false;

        foreach ($companions as $name => $companion)
        {
            if ($companion['cannotbehealed'] ?? false)
            {
                continue;
            }

            $pointsToHeal = $companion['maxhitpoints'] - $companion['hitpoints'];

            if ($pointsToHeal > 0)
            {
                $healable   = true;
                $costToHeal = \round(\log($session['user']['level'] + 1) * ($pointsToHeal + 10) * 1.33);

                $nav  = 'nav.companion.heal.not.have';
                $link = '';

                if ($session['user']['gold'] >= $costToHeal)
                {
                    $nav  = 'nav.companion.heal.have';
                    $link = 'mercenarycamp.php?op=heal&name='.\rawurlencode($name);
                }

                $this->navigation->addNav($nav, $link, [
                    'params' => [
                        'name'     => $companion['name'],
                        'costGold' => $costToHeal,
                    ],
                ]);
            }
        }

        return $healable;
    }
}
