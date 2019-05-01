<?php

require_once 'lib/gamelog.php';

modulehook('clan-withdraw', [
    'clanid' => $session['user']['clanid'],
    'clanrank' => $session['user']['clanrank'],
    'acctid' => $session['user']['acctid']
]);

$charRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Characters::class);

if ($session['user']['clanrank'] >= CLAN_LEADER)
{
    //-- Check if clan have more leaders
    $leadersCount = $charRepository->getClanLeadersCount($session['user']['clanid']);

    if (1 == $leadersCount || 0 == $leadersCount)
    {
        $result = $charRepository->getViableLeaderForClan($session['user']['clanid'], $session['user']['acctid']);

        if ($result)
        {
            //-- there is no alternate leader, let's promote the
            //-- highest ranking member (or oldest member in the
            //-- event of a tie).  This will capture even people
            //-- who applied for membership.
            $charRepository->setNewClanLeader($result['id']);

            \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('section.withdraw.message.promoting.leader', [
                'name' => $result['name'],
                'sex' => $result['sex']
            ], $textDomain));
        }
        else
        {
            $clanRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Clans::class);
            $clanEntity = $clanRepository->find($session['user']['clanid']);

            //-- There are no other members, we need to delete the clan.
            modulehook('clan-delete', ['clanid' => $session['user']['clanid'], 'clanEntity' => $clanEntity]);

            \Doctrine::remove($clanEntity);
            \Doctrine::flush();

            //just in case we goofed, we don't want to have to worry
            //about people being associated with a deleted clan.
            $charRepository->expelPlayersFromDeletedClan($session['user']['clanid']);

            \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('section.withdraw.message.deleting.clan', [], $textDomain));

            gamelog('Clan '.$session['user']['clanid'].' has been deleted, last member gone', 'Clan');

            unset($clanEntity);
        }
    }
}

$mailRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Mail::class);

$subj = ['section.withdraw.mail.subject', ['name' => $session['user']['name']], $textDomain];
$msg = ['section.withdraw.mail.message', ['name' => $session['user']['name']], $textDomain];

$mailRepository->deleteMailFromClanBySubj(serialize($subj));

$leaders = $charRepository->getLeadersFromClan($session['user']['clanid'], $session['user']['acctid']);

foreach($leaders as $leader)
{
    systemmail($leader['acctid'], $subj, $msg);
}

debuglog($session['user']['login'].' has withdrawn from his/her clan nº. '.$session['user']['clanid']);

$session['user']['clanid'] = 0;
$session['user']['clanrank'] = CLAN_APPLICANT;
$session['user']['clanjoindate'] = new \DateTime('0000-00-00 00:00:00');

\LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('section.withdraw.message.withdraw', [], $textDomain));

return redirect('clan.php');
