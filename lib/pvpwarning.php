<?php

// addnews ready
// translator ready
// mail ready
function pvpwarning($dokill = false)
{
    global $session, $textDomain;

    $days = (int) getsetting('pvpimmunity', 5);
    $exp = (int) getsetting('pvpminexp', 1500);

    if ($session['user']['age'] <= $days && 0 == $session['user']['dragonkills'] && 0 == $session['user']['pk'] && $session['user']['experience'] <= $exp)
    {
        if ($dokill)
        {
            \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.warning.pk', [], $textDomain));
            $session['user']['pk'] = 1;
        }
        else
        {
            \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.warning.msg', [ 'days' => $days, 'exp' => $exp ], $textDomain));
        }
    }
    modulehook('pvpwarning', ['dokill' => $dokill]);
}
