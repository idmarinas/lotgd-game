<?php

\LotgdNavigation::addHeader('category.options');

modulehook('clan-enter');

if ('withdraw' == $op)
{
    $mailRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Mail::class);
    \LotgdFlashMessages::addInfoMessage('section.applicant.message.withdraw', [], $textDomain);

    $session['user']['clanid'] = 0;
    $session['user']['clanrank'] = CLAN_APPLICANT;
    $session['user']['clanjoindate'] = new \DateTime('0000-00-00 00:00:00');
    $claninfo = [];

    $subj = ['section.withdraw.mail.subject', ['name' => $session['user']['name']], $textDomain];

    $mailRepository->deleteMailFromSystemBySubj(serialize($subj));
}

if (($claninfo['clanid'] ?? 0) > 0)
{
    //-- Applied for membership to a clan
    \LotgdNavigation::addNav('Waiting Area', 'clan.php?op=waiting');
    \LotgdNavigation::addNav('Withdraw Application', 'clan.php?op=withdraw');
}
else
{
    //-- Hasn't applied for membership to any clan.
    \LotgdNavigation::addNav('Apply for Membership to a Clan', 'clan.php?op=apply');
    \LotgdNavigation::addNav('Apply for a New Clan', 'clan.php?op=new');
}
