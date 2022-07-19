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

trait SetSpecialtyTrait
{
    protected function setSpecialty(Request $request, string $resline)
    {
        global $session;

        $setspecialty = (string) $request->query->get('setspecialty');

        if ('' != $setspecialty)
        {
            $session['user']['specialty'] = $setspecialty;
            $this->dispatcher->dispatch(new Core(), Core::SPECIALTY_SET);
            $this->navigation->addNav('nav.continue', "newday.php?continue=1{$resline}");
        }
        else
        {
            $this->addFlash('info', $this->translator->trans('flash.message.choose.specialty', [], $this->getTranslationDomain()));
            $this->dispatcher->dispatch(new Core(), Core::SPECIALTY_CHOOSE);
        }

        //-- Have navs
        if ($this->navigation->checkNavs())
        {
            //-- Init page
            $this->response->pageStart('title.specialty.choose', [], $this->getTranslationDomain());
            //-- Finalize page
            $this->response->pageEnd();
        }

        $params['tpl'] = 'specialty';

        //-- Init page
        $this->response->pageStart('title.specialty.not', [], $this->getTranslationDomain());

        $params['isAdmin'] = ($session['user']['superuser'] & (SU_MEGAUSER | SU_MANAGE_MODULES));

        $session['user']['specialty'] = 'MP';
        $this->navigation->addNav('nav.continue', "newday.php?continue=1{$resline}");

        //-- Finalize page
        $this->response->pageEnd();
    }
}
