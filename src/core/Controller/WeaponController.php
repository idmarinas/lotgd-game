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

use Lotgd\Core\EntityRepository\WeaponsRepository;
use Lotgd\Core\Events;
use Lotgd\Core\Http\Request;
use Lotgd\Core\Log;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class WeaponController extends AbstractController
{
    private $dispatcher;
    private $log;
    private $repository;

    public function __construct(EventDispatcherInterface $eventDispatcher, Log $log)
    {
        $this->dispatcher = $eventDispatcher;
        $this->log        = $log;
    }

    public function buy(array $params, Request $request): Response
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

                $this->log->debug(\sprintf('spent "%s" gold on the "%s" weapon', ($row['value'] - $params['tradeinvalue']), $row['weaponname']));
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

    public function index(array $params): Response
    {
        global $session;

        $params['opt'] = 'default';
        $weaponLevel   = $this->getRepository()->getMaxWeaponLevel($session['user']['dragonkills']);

        $result = $this->getRepository()->findByLevel($weaponLevel);

        $params['weapons'] = $result;

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
        $params = modulehook('page-weapon-tpl-params', $args->getArguments());

        return $this->render('page/weapon.html.twig', $params);
    }
}
