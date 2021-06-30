<?php

function get_partner($player = false)
{
    global $session;

    if ( ! isset($session['user']['prefs']['sexuality']) || '' == $session['user']['prefs']['sexuality'])
    {
        $session['user']['prefs']['sexuality'] = ! $session['user']['sex'];
    }

    if (false === $player)
    {
        $partner = LotgdSetting::getSetting('barmaid');

        if (SEX_MALE == $session['user']['prefs']['sexuality'])
        {
            $partner = LotgdSetting::getSetting('bard');
        }
    }
    else
    {
        if (INT_MAX == $session['user']['marriedto'])
        {
            $partner = LotgdSetting::getSetting('barmaid');

            if (SEX_MALE == $session['user']['prefs']['sexuality'])
            {
                $partner = LotgdSetting::getSetting('bard');
            }
        }
        else
        {
            $repository = \Doctrine::getRepository('LotgdCore:Accounts');
            $name       = $repository->getCharacterNameFromAcctId($session['user']['marriedto']);

            if ($name)
            {
                $partner = $name;
            }
            else
            {
                $session['user']['marriedto'] = 0;
                $partner                      = LotgdSetting::getSetting('barmaid', '`%Violet`0');

                if (SEX_MALE == $session['user']['prefs']['sexuality'])
                {
                    $partner = LotgdSetting::getSetting('bard', '`^Seth`0');
                }
            }
        }
    }

    return $partner;
}
