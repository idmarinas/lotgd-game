<?php

$acctRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Accounts::class);
$charRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);

$result = $acctRepository->getClanAuthorNameOfMotdDescFromAcctId($claninfo['motdauthor'], $claninfo['descauthor']);
$params['motdAuthorName'] = $result['motdauthname'];
$params['descAuthorName'] = $result['descauthname'];
unset($result);

$params['leaders'] = $charRepository->getClanLeadersCount($claninfo['clanid']);
$params['promotingLeader'] = false;

if (0 == $params['leaders'])
{
    //There's no leader here, probably because the leader's account expired.
    $result = $charRepository->getViableLeaderForClan($session['user']['clanid']);

    if ($result)
    {
        $charRepository->setNewClanLeader($result['id']);
        $params['newLeader'] = $result['name'];

        if ($result['acctid'] == $session['user']['acctid'])
        {
            //if it's the current user, we'll need to update their
            //session in order for the db write to take effect.
            $session['user']['clanrank'] = CLAN_LEADER;
        }
        $params['promotingLeader'] = true;
    }
}

$params['membership'] = $charRepository->getClanMembershipDetails($claninfo['clanid']);

\LotgdNavigation::addHeader('category.options');
if ($session['user']['clanrank'] > CLAN_MEMBER)
{
    \LotgdNavigation::addNav('nav.default.update', 'clan.php?op=motd');
}

\LotgdNavigation::addNav('nav.default.membership', 'clan.php?op=membership');
\LotgdNavigation::addNav('nav.default.online', 'list.php?op=clan');
\LotgdNavigation::addNav('nav.default.waiting.area', 'clan.php?op=waiting');
\LotgdNavigation::addNav('nav.default.withdraw', 'clan.php?op=withdraw', [
    'attributes' => [
        'data-options' => json_encode(['text' => \LotgdTranslator::t('section.withdraw.confirm', [], $textDomain)]),
        'onclick' => 'Lotgd.confirm(this, event)'
]]);
