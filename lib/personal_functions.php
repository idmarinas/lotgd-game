<?php

function killplayer($explossproportion = 0.1, $goldlossproportion = 1)
{
    global $session;

    $args = ['explossproportion' => $explossproportion,
        'goldlossproportion' => $goldlossproportion];
    $args = modulehook('killedplayer', $args);

    if (isset($args['donotkill']))
    {
        return;
    }

    $exp = $session['user']['experience'];
    $exploss = round($exp * $args['explossproportion']);

    if ($exploss > $exp)
    {
        $exploss = $exp;
    }

    if ($exploss > 0)
    {
        $session['user']['experience'] -= $exploss;
        output('`$You lose %s experience.`n', $exploss);
    }

    $gold = $session['user']['gold'];
    $goldloss = round($gold * $args['goldlossproportion']);

    if ($goldloss > $gold)
    {
        $goldloss = $gold;
    }

    if ($goldloss > 0)
    {
        $session['user']['gold'] -= $goldloss;
        output('`$You lose %s gold.`n', $goldloss);
    }

    $session['user']['hitpoints'] = 0;
    $session['user']['alive'] = false;

    \LotgdNavigation::addNav('Daily news', 'news.php');
    page_footer();
}
