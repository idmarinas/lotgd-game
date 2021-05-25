<?php

use Lotgd\Core\Event\Clan;

require_once 'lib/gamelog.php';

$args = new Clan([
    'clanid'   => $session['user']['clanid'],
    'clanrank' => $session['user']['clanrank'],
    'acctid'   => $session['user']['acctid'],
]);
\LotgdEventDispatcher::dispatch($args, Clan::WITHDRAW);
modulehook('clan-withdraw', $args->getData());

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

            \LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.withdraw.promoting.leader', [
                'name' => $result['name'],
                'sex'  => $result['sex'],
            ], $textDomain));
        }
        else
        {
            $clanRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Clans::class);
            $clanEntity     = $clanRepository->find($session['user']['clanid']);

            //-- There are no other members, we need to delete the clan.
            $args = new Clan(['clanid' => $session['user']['clanid'], 'clanEntity' => $clanEntity]);
            \LotgdEventDispatcher::dispatch($args, Clan::DELETE);
            modulehook('clan-delete', $args->getData());

            \Doctrine::remove($clanEntity);
            \Doctrine::flush();

            //just in case we goofed, we don't want to have to worry
            //about people being associated with a deleted clan.
            $charRepository->expelPlayersFromDeletedClan($session['user']['clanid']);

            \LotgdFlashMessages::addErrorMessage(\LotgdTranslator::t('flash.message.withdraw.deleting.clan', [], $textDomain));

            gamelog('Clan '.$session['user']['clanid'].' has been deleted, last member gone', 'Clan');

            unset($clanEntity);
        }
    }
}

$mailRepository = \Doctrine::getRepository(\Lotgd\Core\Entity\Mail::class);

$subj = ['mail.withdraw.subject', ['name' => $session['user']['name']], $textDomain];
$msg  = ['mail.withdraw.message', ['name' => $session['user']['name']], $textDomain];

$mailRepository->deleteMailFromSystemBySubj(\serialize($subj));

$leaders = $charRepository->getLeadersFromClan($session['user']['clanid'], $session['user']['acctid']);

foreach ($leaders as $leader)
{
    systemmail($leader['acctid'], $subj, $msg);
}

debuglog($session['user']['login'].' has withdrawn from his/her clan nº. '.$session['user']['clanid']);

$session['user']['clanid']       = 0;
$session['user']['clanrank']     = CLAN_APPLICANT;
$session['user']['clanjoindate'] = new \DateTime('0000-00-00 00:00:00');

\LotgdFlashMessages::addInfoMessage(\LotgdTranslator::t('flash.message.withdraw.withdraw', [], $textDomain));

return redirect('clan.php');
