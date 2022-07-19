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

trait SetRaceTrait
{
    protected function setRace(Request $request, string $resline)
    {
        global $session;

        $setrace = (string) $request->query->get('setrace');

        if ('' != $setrace)
        {
            $vname = $this->settings->getSetting('villagename', LOCATION_FIELDS);
            //in case the module wants to reference it this way.
            $session['user']['race'] = $setrace;
            // Set the person to the main village/capital by default
            $session['user']['location'] = $vname;
            $this->dispatcher->dispatch(new Core(), Core::RACE_SET);
            $this->navigation->addNav('nav.continue', "newday.php?continue=1{$resline}");
        }
        else
        {
            $this->addFlash('info', $this->translator->trans('flash.message.choose.race', [], $this->getTranslationDomain()));
            $this->dispatcher->dispatch(new Core(), Core::RACE_CHOOSE);
        }

        //-- Have navs
        if ($this->navigation->checkNavs())
        {
            //-- Init page
            $this->response->pageStart('title.race.choose', [], $this->getTranslationDomain());
            //-- Finalize page
            $this->response->pageEnd();
        }

        $params['tpl'] = 'race';

        //-- Init page
        $this->response->pageStart('title.race.not', [], $this->getTranslationDomain());

        $params['isAdmin'] = ($session['user']['superuser'] & (SU_MEGAUSER | SU_MANAGE_MODULES));

        $session['user']['race'] = 'app_default'; // Default race

        $this->navigation->addNav('nav.continue', "newday.php?continue=1{$resline}");

        //-- Finalize page
        $this->response->pageEnd();
    }
}
