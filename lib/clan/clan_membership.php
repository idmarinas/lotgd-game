<?php

use Lotgd\Core\Event\Clan;

\LotgdNavigation::addHeader('category.options');
\LotgdNavigation::addNav('nav.membership.hall', 'clan.php');

$charRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);

$setrank   = (int) \LotgdRequest::getPost('setrank');
$whoacctid = (int) \LotgdRequest::getPost('whoacctid');
$remove    = (int) \LotgdRequest::getQuery('remove');

if ($remove)
{
    $character = $charRepository->getCharacterFromAcctidAndRank($remove, $session['user']['clanrank']);

    $args = new Clan([
        'setrank' => 0,
        'login'   => $character->getAcct()->getLogin(),
        'name'    => $character->getName(),
        'acctid'  => $remove,
        'clanid'  => $session['user']['clanid'],
        'oldrank' => $character->getClanrank(),
    ]);

    \LotgdEventDispatcher::dispatch($args, Clan::RANK_SET);
    $args = modulehook('clan-setrank', $args->getData());

    $character->setClanrank(CLAN_APPLICANT)
        ->setClanid(0)
        ->setClanjoindate(new DateTime('0000-00-00 00:00:00'))
    ;

    \Doctrine::persist($character);
    \Doctrine::flush();

    \LotgdLog::debug("Player {$session['user']['name']} removed player {$character->getAcct()->getLogin()} from {$claninfo['clanname']}.", $remove);

    //delete unread application emails from this user.
    //breaks if the applicant has had their name changed via
    //dragon kill, superuser edit, or lodge color change
    $subj = \serialize(['mail.apply.subject', ['name' => $character->getName()], $textDomain]);

    $mailRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Mail::class);
    $mailRepository->deleteMailFromSystemBySubj($subj);

    unset($character);
}
elseif ($setrank > 0 && $setrank <= $session['user']['clanrank'] && $whoacctid)
{
    $character = $charRepository->findOneBy(['acct' => $whoacctid]);

    if ($character)
    {
        $args = new Clan([
            'setrank' => $setrank,
            'login'   => $character->getAcct()->getLogin(),
            'name'    => $character->getName(),
            'acctid'  => $whoacctid,
            'clanid'  => $session['user']['clanid'],
            'oldrank' => $character->getClanrank(),
        ]);
        \LotgdEventDispatcher::dispatch($args, Clan::RANK_SET);
        $args = modulehook('clan-setrank', $args->getData());

        if ( ! ($args['handled'] ?? false))
        {
            $character->setClanrank(\max(0, \min($session['user']['clanrank'], $setrank)));

            \LotgdLog::debug("Player {$session['user']['name']} changed rank of {$character->getName()} to {$setrank}.", $whoacctid);

            \Doctrine::persist($character);
            \Doctrine::flush();

            unset($character);
        }
    }
}

$params['validRanks'] = \array_intersect_key($params['ranksNames'], \range(0, $session['user']['clanrank']));
$params['membership'] = $charRepository->getClanMembershipList($claninfo['clanid']);
