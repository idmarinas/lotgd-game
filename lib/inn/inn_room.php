<?php

$config = unserialize($session['user']['donationconfig']);
$expense = round(($session['user']['level'] * (10 + log($session['user']['level']))), 0);
$pay = httpget('pay');

$twig = [
    'pay' => $pay,
    'expense' => $expense,
    'barkeep' => $barkeep
];

if ($pay)
{
    if (2 == $pay || $session['user']['gold'] >= $expense || $session['user']['boughtroomtoday'])
    {
        if ($session['user']['loggedin'])
        {
            if (! $session['user']['boughtroomtoday'])
            {
                if (2 == $pay)
                {
                    $fee = getsetting('innfee', '5%');

                    if (strpos($fee, '%'))
                    {
                        $expense += round($expense * $fee / 100, 0);
                    }
                    else
                    {
                        $expense += $fee;
                    }
                    $session['user']['goldinbank'] -= $expense;
                }
                else
                {
                    $session['user']['gold'] -= $expense;
                }
                $session['user']['boughtroomtoday'] = 1;
                debuglog("spent $expense gold on an inn room");
            }
            $session['user']['location'] = $iname;
            $session['user']['loggedin'] = 0;
            $session['user']['restorepage'] = 'inn.php?op=strolldown';
            saveuser();
        }
        $session = [];
        redirect('index.php');
    }
    else
    {
        $twig['cantbuy'] = true;
    }
}
else
{
    if ($session['user']['boughtroomtoday'])
    {
        addnav('Go to room', 'inn.php?op=room&pay=1');
    }
    else
    {
        modulehook('innrooms');
        $fee = getsetting('innfee', '5%');
        $twig['fee'] = $fee;

        if (strpos($fee, '%'))
        {
            $bankexpense = $expense + round($expense * $fee / 100, 0);
        }
        else
        {
            $bankexpense = $expense + $fee;
        }

        $twig['bankexpense'] = $bankexpense;

        if ($session['user']['goldinbank'] >= $bankexpense && $bankexpense != $expense)
        {
            $twig['feepercent'] = strpos($fee, '%') ? true : false;
        }
        $bodyguards = ['Butch', 'Bruce', 'Alfonozo', 'Guido', 'Bruno', 'Bubba', 'Al', 'Chuck', 'Brutus', 'Nunzio', 'Terrance', 'Mitch', 'Rocco', 'Spike', 'Gregor', 'Sven', 'Draco'];
        $twig['bodyguards'] = $bodyguards;

        addnav(['Give him %s gold', $expense], 'inn.php?op=room&pay=1');

        if ($session['user']['goldinbank'] >= $bankexpense)
        {
            addnav(['Pay %s gold from bank', $bankexpense], 'inn.php?op=room&pay=2');
        }
    }
}

rawoutput($lotgd_tpl->renderThemeTemplate('pages/inn/room.twig', $twig));
