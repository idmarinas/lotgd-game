<?php

function get_partner($player = false)
{
    global $session;

    if (! isset($session['user']['prefs']['sexuality']) || '' == $session['user']['prefs']['sexuality'])
    {
        $session['user']['prefs']['sexuality'] = ! $session['user']['sex'];
    }

    if (false === $player)
    {
        $partner = getsetting('barmaid');

        if (SEX_MALE == $session['user']['prefs']['sexuality'])
        {
            $partner = getsetting('bard');
        }
    }
    else
    {
        if (INT_MAX == $session['user']['marriedto'])
        {
            $partner = getsetting('barmaid');

            if (SEX_MALE == $session['user']['prefs']['sexuality'])
            {
                $partner = getsetting('bard');
            }
        }
        else
        {
            $repository = \Doctrine::getRepository('LotgdCore:Accounts');
            $name = $repository->getCharacterNameFromAcctId($session['user']['marriedto']);

            if ($name)
            {
                $partner = $name;
            }
            else
            {
                $session['user']['marriedto'] = 0;
                $partner = getsetting('barmaid', '`%Violet`0');

                if (SEX_MALE == $session['user']['prefs']['sexuality'])
                {
                    $partner = getsetting('bard', '`^Seth`0');
                }
            }
        }
    }

    return $partner;
}
