<?php

\LotgdNavigation::addHeader('category.options');

modulehook('clan-enter');

if ('withdraw' == $op)
{
    $mailRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Mail::class);

    \LotgdFlashMessages::addInfoMessage(\LotgdSanitize::fullSanitize(\LotgdTranslator::t('flash.message.applicant.withdraw', [
        'clanOwnerName' => $params['clanOwnerName'],
        'clanName' => $claninfo['clanname']
    ], $textDomain)));

    $session['user']['clanid'] = 0;
    $session['user']['clanrank'] = CLAN_APPLICANT;
    $session['user']['clanjoindate'] = new \DateTime('0000-00-00 00:00:00');
    $claninfo = [];

    $subj = ['mail.apply.subject', ['name' => $session['user']['name']], $textDomain];

    $mailRepository->deleteMailFromSystemBySubj(serialize($subj), $session['user']['acctid']);

    $subj = ['mail.desc.reminder.subject', [], $textDomain];

    $mailRepository->deleteMailFromSystemBySubj(serialize($subj), $session['user']['acctid']);
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
