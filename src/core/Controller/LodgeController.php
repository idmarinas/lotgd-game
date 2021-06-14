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
use Lotgd\Core\Navigation\Navigation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class LodgeController extends AbstractController
{
    private $dispatcher;
    private $navigation;

    public function __construct(EventDispatcherInterface $eventDispatcher, Navigation $navigation)
    {
        $this->dispatcher = $eventDispatcher;
        $this->navigation = $navigation;
    }

    public function points(array $params): Response
    {
        $params['tpl'] = 'points';

        $params['currencySymbol'] = getsetting('paypalcurrency', 'USD');
        $params['currencyUnits']  = getsetting('dpointspercurrencyunit', 100);
        $params['refererAward']   = getsetting('refereraward', 25);
        $params['referMinLevel']  = getsetting('referminlevel', 25);

        $params['donatorPointMessages'] = [
            [
                'section.points.messages.default', //-- Translator keys
                [ //-- Params for translator
                    'currencySymbol' => $params['currencySymbol'],
                    'currencyUnits'  => $params['currencyUnits'],
                ],
                $params['textDomain'], //-- Translator text domain
            ],
        ];

        return $this->renderLodge($params);
    }

    public function index(array $params): Response
    {
        $params['tpl'] = 'default';

        if ($params['canEntry'])
        {
            $this->navigation->addHeader('category.use.points');
            $this->dispatcher->dispatch(new GenericEvent(), Events::PAGE_LODGE);
            modulehook('lodge');
        }

        return $this->renderLodge($params);
    }

    private function renderLodge(array $params): Response
    {
        //-- This is only for params not use for other purpose
        $args = new GenericEvent(null, $params);
        $this->dispatcher->dispatch($args, Events::PAGE_LODGE_POST);
        $params = modulehook('page-lodge-tpl-params', $args->getArguments());

        return $this->render('page/lodge.html.twig', $params);
    }
}
