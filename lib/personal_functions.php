<?php

use Lotgd\Core\Event\Character;

function killplayer($explossproportion = 0.1, $goldlossproportion = 1)
{
    global $session;

    $args = new Character([
        'explossproportion'  => $explossproportion,
        'goldlossproportion' => $goldlossproportion,
    ]);
    \LotgdEventDispatcher::dispatch($args, Character::KILLED_PLAYER);
    $args = modulehook('killedplayer', $args->getData());

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
