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
            $sql = 'SELECT name FROM '.DB::prefix('accounts')." WHERE acctid = {$session['user']['marriedto']}";
            $result = DB::query($sql);

            if ($row = DB::fetch_assoc($result))
            {
                $partner = $row['name'];
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
    //	No need to translate names...
    //	tlschema("partner");
    //	$partner = translate_inline($partner);
    //	tlschema();
    return $partner;
}
