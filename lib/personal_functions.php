<?php

function killplayer($explossproportion = 0.1, $goldlossproportion = 1)
{
    global $session;

    $args = [
        'explossproportion'  => $explossproportion,
        'goldlossproportion' => $goldlossproportion,
    ];
    \LotgdHook::trigger(\Lotgd\Core\Hook::HOOK_CHARACTER_KILLED_PLAYER, null, $args);
    $args = modulehook('killedplayer', $args);

    if (isset($args['donotkill']))
    {
        return;
    }

    $exp     = $session['user']['experience'];
    $exploss = \round($exp * $args['explossproportion']);

    if ($exploss > $exp)
    {
        $exploss = $exp;
    }

    if ($exploss > 0)
    {
        $session['user']['experience'] -= $exploss;
        \LotgdResponse::pageAddContent(\LotgdFormat::colorize(\sprintf('`$You lose %s experience.`n', $exploss), true));
    }

    $gold     = $session['user']['gold'];
    $goldloss = \round($gold * $args['goldlossproportion']);

    if ($goldloss > $gold)
    {
        $goldloss = $gold;
    }

    if ($goldloss > 0)
    {
        $session['user']['gold'] -= $goldloss;
        \LotgdResponse::pageAddContent(\LotgdFormat::colorize(\sprintf('`$You lose %s gold.`n', $goldloss), true));
    }

    $session['user']['hitpoints'] = 0;
    $session['user']['alive']     = false;

    \LotgdNavigation::addNav('Daily news', 'news.php');
    \LotgdResponse::pageEnd();
}
