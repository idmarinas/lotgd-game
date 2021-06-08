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
use Lotgd\Core\Log;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ArmorController extends AbstractController
{
    private $dispatcher;
    private $log;

    public function __construct(EventDispatcherInterface $eventDispatcher, Log $log)
    {
        $this->dispatcher = $eventDispatcher;
        $this->log        = $log;
    }

    public function index(array $params): Response
    {
        global $session;

        /** @var Lotgd\Core\EntityRepository\ArmorRepository */
        $repository = $this->getDoctrine()->getRepository(\Lotgd\Core\Entity\Armor::class);

        $armorLevel = $repository->getMaxArmorLevel($session['user']['dragonkills']);

        $result = $repository->findByLevel($armorLevel);

        $params['opt']   = 'list';
        $params['stuff'] = $result;

        return $this->renderArmor($params);
    }

    public function buy(array $params, Request $request): Response
    {
        global $session;

        /** @var Lotgd\Core\EntityRepository\ArmorRepository */
        $repository   = $this->getDoctrine()->getRepository(\Lotgd\Core\Entity\Armor::class);
        $id           = $request->query->getInt('id');
        $tradeinvalue = $params['tradeinvalue'];

        $params['opt']    = 'buy';
        $params['result'] = $repository->findOneArmorById($id);

        if ($params['result'])
        {
            $row             = $params['result'];
            $params['buyIt'] = false;

            if ($row['value'] <= ($session['user']['gold'] + $tradeinvalue))
            {
                $params['buyIt'] = true;

                $this->log->debug(\sprintf('spent "%s" gold on the "%s" armor', ($row['value'] - $tradeinvalue), $row['armorname']));
                $session['user']['gold'] -= $row['value'];
                $session['user']['armor'] = $row['armorname'];
                $session['user']['gold'] += $tradeinvalue;
                $session['user']['defense'] -= $session['user']['armordef'];
                $session['user']['armordef'] = $row['defense'];
                $session['user']['defense'] += $session['user']['armordef'];
                $session['user']['armorvalue'] = $row['value'];
            }
        }

        return $this->renderArmor($params);
    }

    private function renderArmor(array $params): Response
    {
        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_ARMOR_POST);
        $params = modulehook('page-armor-tpl-params', $args->getArguments());

        return $this->render('page/armor.html.twig', $params);
    }
}
