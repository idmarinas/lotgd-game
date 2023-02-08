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
use Lotgd\Core\Repository\WeaponsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class WeaponController extends AbstractController
{
    private $dispatcher;
    private $log;
    private $repository;
    private $response;
    private $navigation;

    public function __construct(EventDispatcherInterface $eventDispatcher, Log $log, HttpResponse $response, Navigation $navigation)
    {
        $this->dispatcher = $eventDispatcher;
        $this->log        = $log;
        $this->response   = $response;
        $this->navigation = $navigation;
    }

    public function index(Request $request): Response
    {
        global $session;

        // Don't hook on to this text for your standard modules please, use "weapon" instead.
        // This hook is specifically to allow modules that do other weapons to create ambience.
        $args = new GenericEvent(null, ['textDomain' => 'page_weapon', 'textDomainNavigation' => 'navigation_weapon']);
        $this->dispatcher->dispatch($args, Events::PAGE_WEAPONS_PRE);
        $result               = $args->getArguments();
        $textDomain           = $result['textDomain'];
        $textDomainNavigation = $result['textDomainNavigation'];
        unset($result);

        $tradeinvalue = round(($session['user']['weaponvalue'] * .75), 0);

        $params = [
            'textDomain'   => $textDomain,
            'tradeinvalue' => $tradeinvalue,
        ];

        //-- Init page
        $this->response->pageTitle('title', [], $textDomain);

        $op = (string) $request->query->get('op');

        //-- Change text domain for navigation
        $this->navigation->setTextDomain($textDomainNavigation);

        $method = method_exists($this, $op) ? $op : 'enter';

        $this->navigation->villageNav();

        return $this->{$method}($params, $request);
    }

    protected function enter(array $params): Response
    {
        global $session;

        $params['opt'] = 'default';
        $weaponLevel   = $this->getRepository()->getMaxWeaponLevel($session['user']['dragonkills']);

        $result = $this->getRepository()->findByLevel($weaponLevel);

        $params['weapons'] = $result;

        return $this->renderWeapon($params);
    }

    protected function buy(array $params, Request $request): Response
    {
        global $session;

        $id = $request->query->getInt('id');

        $params['opt']    = 'buy';
        $params['result'] = $this->getRepository()->findOneWeaponById($id);

        if ($params['result'])
        {
            $row             = $params['result'];
            $params['buyIt'] = false;

            if ($row['value'] <= ($session['user']['gold'] + $params['tradeinvalue']))
            {
                $params['buyIt'] = true;

                $this->log->debug(sprintf('spent "%s" gold on the "%s" weapon', ($row['value'] - $params['tradeinvalue']), $row['weaponname']));
                $session['user']['gold'] -= $row['value'];
                $session['user']['weapon'] = $row['weaponname'];
                $session['user']['gold'] += $params['tradeinvalue'];
                $session['user']['attack'] -= $session['user']['weapondmg'];
                $session['user']['weapondmg'] = $row['damage'];
                $session['user']['attack'] += $session['user']['weapondmg'];
                $session['user']['weaponvalue'] = $row['value'];
            }
        }

        return $this->renderWeapon($params);
    }

    private function getRepository(): WeaponsRepository
    {
        if ( ! $this->repository instanceof WeaponsRepository)
        {
            $this->repository = $this->getDoctrine()->getRepository('LotgdCore:Weapons');
        }

        return $this->repository;
    }

    private function renderWeapon(array $params): Response
    {
        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_WEAPONS_POST);
        $params = $args->getArguments();

        //-- Restore text domain for navigation
        $this->navigation->setTextDomain();

        return $this->render('page/weapon.html.twig', $params);
    }
}
