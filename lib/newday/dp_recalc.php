<?php

$pdks = [];
reset($labels);

foreach ($labels as $type => $label)
{
    $head = explode(',', $label);

    if (count($head) > 1)
    {
        continue;
    } //got a headline here
    $pdks[$type] = (int) \LotgdHttp::getPost($type);
    $pdktotal += (int) $pdks[$type];

    if ((int) $pdks[$type] < 0)
    {
        $pdkneg = true;
    }
}
$pdktotal = 0;
$pdkneg = false;
modulehook('pdkpointrecalc');

foreach ($labels as $type => $label)
{
    $head = explode(',', $label);

    if (count($head) > 1)
    {
        continue;
    } //got a headline here
    $pdktotal += (int) $pdks[$type];

    if ((int) $pdks[$type] < 0)
    {
        $pdkneg = true;
    }
}

if ($pdktotal == $dkills - $dp && ! $pdkneg)
{
    $dp += $pdktotal;
    $session['user']['maxhitpoints'] += (5 * $pdks['hp']);
    $session['user']['strength'] += $pdks['str'];
    $session['user']['dexterity'] += $pdks['dex'];
    $session['user']['intelligence'] += $pdks['int'];
    $session['user']['constitution'] += $pdks['con'];
    $session['user']['wisdom'] += $pdks['wis'];

    reset($labels);

    foreach ($labels as $type => $label)
    {
        $head = explode(',', $label);

        if (count($head) > 1)
        {
            continue;
        } //got a headline here
        $count = 0;

        if (isset($pdks[$type]))
        {
            $count = (int) $pdks[$type];
        }

        while ($count)
        {
            $count--;
            array_push($session['user']['dragonpoints'], $type);
        }
    }
}
else
{
    \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.dragon.point.error', [], $textDomain));
}
