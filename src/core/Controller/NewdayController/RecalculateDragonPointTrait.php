<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.1.0
 */

namespace Lotgd\Core\Controller\NewdayController;

use Lotgd\Core\Event\Core;
use Lotgd\Core\Http\Request;

trait RecalculateDragonPointTrait
{
    protected function recalculateDragonPoints(array $labels, int &$dp, Request $request): void
    {
        global $session;

        $pdks = [];
        $dkills = $session['user']['dragonkills'];

        $pdktotal = 0;
        $pdkneg   = false;

        foreach ($labels as $type => $label)
        {
            $head = explode(',', $label);

            if (\count($head) > 1)
            {
                continue;
            } //got a headline here
            $pdks[$type] = $request->request->getInt($type);
            $pdktotal += $pdks[$type];

            if ((int) $pdks[$type] < 0)
            {
                $pdkneg = true;
            }
        }

        $this->dispatcher->dispatch(new Core(), Core::DK_POINT_RECALC);
        modulehook('pdkpointrecalc');

        if ($pdktotal != $dkills - $dp || $pdkneg)
        {
            $this->addFlash('error', $this->translator->trans('flash.message.dragon.point.error', [], $this->getTranslationDomain()));

            return;
        }

        $dp += $pdktotal;

        $session['user']['maxhitpoints'] += (5 * $pdks['hp']);
        $session['user']['strength']     += $pdks['str'];
        $session['user']['dexterity']    += $pdks['dex'];
        $session['user']['intelligence'] += $pdks['int'];
        $session['user']['constitution'] += $pdks['con'];
        $session['user']['wisdom']       += $pdks['wis'];

        foreach ($labels as $type => $label)
        {
            $head = explode(',', $label);

            if (\count($head) > 1)
            {
                continue;
            } //got a headline here

            $count = isset($pdks[$type]) ? $pdks[$type] : 0;

            while ($count)
            {
                --$count;
                $session['user']['dragonpoints'][] = $type;
            }
        }
    }
}
