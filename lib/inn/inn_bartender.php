<?php

if ('bribe' == $action)
{
    $amt = (int) \LotgdHttp::getQuery('amt');
    $type = (string) \LotgdHttp::getQuery('type');

    $params['type'] = $type;
    $params['amount'] = $amt;

    $g1 = $session['user']['level'] * 10;
    $g2 = $session['user']['level'] * 50;
    $g3 = $session['user']['level'] * 100;

    if ('' == $type)
    {
        \LotgdNavigation::addHeader($params['barkeep'], [ 'translate' => false ]);
        \LotgdNavigation::addNav('nav.bribe.gem', 'inn.php?op=bartender&act=bribe&type=gem&amt=1', [
            'params' => [
                'gem' => 1
            ]
        ]);
        \LotgdNavigation::addNav('nav.bribe.gem', 'inn.php?op=bartender&act=bribe&type=gem&amt=2', [
            'params' => [
                'gem' => 2
            ]
        ]);
        \LotgdNavigation::addNav('nav.bribe.gem', 'inn.php?op=bartender&act=bribe&type=gem&amt=3', [
            'params' => [
                'gem' => 3
            ]
        ]);

        \LotgdNavigation::addNav('nav.bribe.gold', "inn.php?op=bartender&act=bribe&type=gold&amt=$g1", [
            'params' => [
                'gold' => $g1
            ]
        ]);
        \LotgdNavigation::addNav('nav.bribe.gold', "inn.php?op=bartender&act=bribe&type=gold&amt=$g2", [
            'params' => [
                'gold' => $g2
            ]
        ]);
        \LotgdNavigation::addNav('nav.bribe.gold', "inn.php?op=bartender&act=bribe&type=gold&amt=$g3", [
            'params' => [
                'gold' => $g3
            ]
        ]);
    }
    else
    {
        if ('gem' == $type)
        {
            if ($session['user']['gems'] < $amt)
            {
                \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.bribe.no.gems', [ 'amt' => $amt ], $textDomain));

                return redirect('inn.php?op=bartender&act=bribe');
            }
            else
            {
                $chance = $amt * 30;
                $session['user']['gems'] -= $amt;

                debuglog("spent $amt gems on bribing $barkeep");
            }
        }
        else
        {
            if ($session['user']['gold'] < $amt)
            {
                \LotgdFlashMessages::addWarningMessage(\LotgdTranslator::t('flash.message.bribe.no.gold', [ 'amt' => $amt ], $textDomain));

                return redirect('inn.php?op=bartender&act=bribe');
            }
            else
            {
                $sfactor = 50 / 90;
                $fact = $amt / $session['user']['level'];
                $chance = ($fact - 10) * $sfactor + 25;
                $session['user']['gold'] -= $amt;

                debuglog("spent $amt gold bribing $barkeep");
            }
        }

        $params['bribeSuccess'] = mt_rand(0, 100) < $chance;

        if ($params['bribeSuccess'])
        {
            \LotgdNavigation::addHeader('category.want');
            modulehook('bartenderbribe', []);

            if (getsetting('pvp', 1))
            {
                \LotgdNavigation::addNav('nav.bribe.upstairs', 'inn.php?op=bartender&act=listupstairs');
            }
            \LotgdNavigation::addNav('nav.bribe.color', 'inn.php?op=bartender&act=colors');

            if (getsetting('allowspecialswitch', true))
            {
                \LotgdNavigation::addNav('nav.bribe.specialty', 'inn.php?op=bartender&act=specialty');
            }
        }
        else
        {
            \LotgdNavigation::addNav('nav.barkeep.again', 'inn.php?op=bartender', [
                'params' => [
                    'barkeep' => $params['barkeep']
                ]
            ]);
        }
    }
}
elseif ('listupstairs' == $action)
{
    $pvp = \LotgdLocator::get(\Lotgd\Core\Pvp\Listing::class);

    $pvptime = getsetting('pvptimeout', 600);

    $params['paginator'] = $pvp->getPvpList($params['innName']);
    $params['sleepers'] = $pvp->getLocationSleepersCount($params['innName']);
    $params['returnLink'] = \LotgdHttp::getServer('REQUEST_URI');
    $params['pvpTimeOut'] = new \DateTime(date('Y-m-d H:i:s', strtotime("-$pvptime seconds")));

    \LotgdNavigation::addNav('Refresh the list', 'inn.php?op=bartender&act=listupstairs');
}
elseif ('colors' == $action)
{
    $outputColor = \LotgdLocator::get(\Lotgd\Core\Output\Collector::class);

    $params['testText'] = (string) \LotgdHttp::getPost('testText');
    $params['formUrl'] = (string) \LotgdHttp::getServer('REQUEST_URI');

    $colors = $outputColor->getColors();

    $params['colors'] = array_map(function ($n) {
        return "`{$n}&#96;{$n} - &#180;{$n}´{$n}";
    }, array_keys($colors));

    $params['colors'] = '<span class="ui basic small labels"><span class="ui label">'.implode('</span> <span class="ui label">', $params['colors']).'</span></span>';
}
elseif ('specialty' == $action)
{
    $specialty = (string) \LotgdHttp::getQuery('specialty');
    $uri = (string) \LotgdHttp::getServer('REQUEST_URI');

    $params['specialty'] = $specialty;

    if ('' == $specialty)
    {
        $specialities = modulehook('specialtynames');

        \LotgdNavigation::addHeader('category.specialty');
        foreach ($specialities as $key => $name)
        {
            \LotgdNavigation::addNavNotl($name, \LotgdSanitize::cmdSanitize($uri."&specialty={$key}"));
        }
    }
    else
    {
        $session['user']['specialty'] = $specialty;
    }
}
else
{
    \LotgdNavigation::addHeader(\LotgdSanitize::fullSanitize($params['barkeep']), [
        'translation' => false
    ]);
    \LotgdNavigation::addNav('Bribe', 'inn.php?op=bartender&act=bribe');
    \LotgdNavigation::addHeader('Drinks');

    modulehook('ale', []);
}
