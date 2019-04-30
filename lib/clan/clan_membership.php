<?php

\LotgdNavigation::addHeader('category.options');
\LotgdNavigation::addNav('nav.membership.hall', 'clan.php');

$charRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);

$setrank = (int) \LotgdHttp::getPost('setrank');
$whoacctid = (int) \LotgdHttp::getPost('whoacctid');
$remove = (int) \LotgdHttp::getQuery('remove');

if ($remove)
{
    $character = $charRepository->getCharacterFromAcctidAndRank($remove, $session['user']['clanrank']);

    $args = modulehook('clan-setrank', [
        'setrank' => 0,
        'login' => $character->getAcct()->getLogin(),
        'name' => $character->getName(),
        'acctid' => $remove,
        'clanid' => $session['user']['clanid'],
        'oldrank' => $character->getClanrank()
    ]);

    $character->setClanrank(CLAN_APPLICANT)
        ->setClanid(0)
        ->setClanjoindate(new DateTime('0000-00-00 00:00:00'))
    ;

    \Doctrine::persist($character);
    \Doctrine::flush();

    debuglog("Player {$session['user']['name']} removed player {$character->getAcct()->getLogin()} from {$claninfo['clanname']}.", $remove);

    //delete unread application emails from this user.
    //breaks if the applicant has had their name changed via
    //dragon kill, superuser edit, or lodge color change
    $subj = serialize(['section.apply.mail.subject', ['name' => $character->getName()], $textDomain]);

    $mailRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Mail::class);
    $mailRepository->deleteMailFromClanBySubj($subj);

    unset($character);
}
elseif ($setrank > 0 && $setrank <= $session['user']['clanrank'] && $whoacctid)
{
    $character = $charRepository->findOneBy(['acct' => $whoacctid]);

    if ($character)
    {
        $args = modulehook('clan-setrank', [
            'setrank' => $setrank,
            'login' => $character->getAcct()->getLogin(),
            'name' => $character->getName(),
            'acctid' => $whoacctid,
            'clanid' => $session['user']['clanid'],
            'oldrank' => $character->getClanrank()
        ]);

        if (! ($args['handled'] ?? false))
        {
            $character->setClanrank(max(0, min($session['user']['clanrank'], $setrank)));

            debuglog("Player {$session['user']['name']} changed rank of {$character->getName()} to {$setrank}.", $whoacctid);

            \Doctrine::persist($character);
            \Doctrine::flush();

            unset($character);
        }
    }
}

$params['validRanks'] = array_intersect_key($params['ranksNames'], range(0, $session['user']['clanrank']));
$params['membership'] = $charRepository->getClanMembershipList($claninfo['clanid']);
