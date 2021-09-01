<?php

use Lotgd\Core\Event\Graveyard;

if ($session['user']['gravefights'] <= 0)
{
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.no.torments', [], $textDomain));
    $op = '';
    \LotgdRequest::setQuery('op', '');
}
else
{
    /** @var \Lotgd\Core\Tool\CreatureFunction */
    $creaturefunctions = LotgdKernel::get('lotgd_core.tool.creature_functions');
    /** @var \Lotgd\Core\Combat\Battle */
    $serviceBattle = LotgdKernel::get('lotgd_core.combat.battle');

    $serviceBattle->suspendCompanions('allowinshades', true);

    /** New occurrence dispatcher for special events. */
    /** @var \Lotgd\CoreBundle\OccurrenceBundle\OccurrenceEvent */
    $event = \LotgdKernel::get('occurrence_dispatcher')->dispatch('graveyard', null, [
        'translation_domain'            => $textDomain,
        'translation_domain_navigation' => $textDomainNavigation,
        'route'                         => 'graveyard.php',
        'navigation_method'             => 'graveyardNav',
    ]);

    if ($event->isPropagationStopped())
    {
        \LotgdResponse::pageEnd();
    }
    elseif ($event['skip_description'])
    {
        $skipgraveyardtext = true;
        $op                = '';
        \LotgdRequest::setQuery('op', '');
    }
    //-- Only execute when NOT occurrence is in progress.
    elseif (0 != module_events('graveyard', LotgdSetting::getSetting('gravechance', 0)))
    {
        if (\LotgdNavigation::checkNavs())
        {
            \LotgdResponse::pageEnd();
        }

        // If we're going back to the graveyard, make sure to reset
        // the special and the specialmisc
        $session['user']['specialinc']  = '';
        $session['user']['specialmisc'] = '';

        $skipgraveyardtext = true;
        $op                = '';
        \LotgdRequest::setQuery('op', '');
    }
    else
    {
        --$session['user']['gravefights'];
        $level  = (int) $session['user']['level'];
        $battle = true;
        $result = $creaturefunctions->lotgdSearchCreature(1, $level, $level, false, false);

        $badguy = $creaturefunctions->lotgdTransformCreature($result[0]);
        $badguy['creaturehealth']    += 50;
        $badguy['creaturemaxhealth'] += 50;

        // Make graveyard creatures easier.
        $badguy['creatureattack']  *= .7;
        $badguy['creaturedefense'] *= .7;

        // Add enemy
        $attackstack['enemies'][0]      = $badguy;
        $attackstack['options']['type'] = 'graveyard';

        //no multifights currently, so this hook passes the badguy to modify
        $attackstack = new Graveyard($attackstack);
        \LotgdEventDispatcher::dispatch($attackstack, Graveyard::FIGHT_START);
        $attackstack = modulehook('graveyardfight-start', $attackstack->getData());

        $session['user']['badguy'] = $attackstack;
    }
}
