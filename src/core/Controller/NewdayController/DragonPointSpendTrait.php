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

trait DragonPointSpendTrait
{
    protected function dragonPointSpend(array $labels, array &$params, int $dkills, int $dp, array $canbuy, string $resline)
    {
        global $session;

        //-- Init page
        $this->response->pageStart('title.dragonpoints', [], $this->getTranslationDomain());

        reset($labels);

        $params['tpl']          = 'dragonpoints';
        $params['points']       = $dkills - $dp;
        $params['labels']       = $labels;
        $params['canBuy']       = $canbuy;
        $params['distribution'] = array_count_values($session['user']['dragonpoints']);

        //-- More than 1 unallocated point
        if ($params['points'] > 1)
        {
            $this->navigation->addNav('nav.reset', "newday.php?pdk=0{$resline}");

            $params['formUrl'] = "newday.php?pdk=1{$resline}";
        }
        //-- 1 unallocated point
        else
        {
            foreach ($labels as $type => $label)
            {
                $head = explode(',', $label);

                if (\count($head) > 1)
                {
                    $this->navigation->addHeader("category.{$type}");

                    continue;
                }

                if ($canbuy[$type] ?? false)
                {
                    $this->navigation->addNav("nav.{$type}", "newday.php?dk={$type}{$resline}");
                }
            }
        }
    }
}
