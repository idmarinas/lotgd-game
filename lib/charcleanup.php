<?php

function char_cleanup($accountId, $type): bool
{
    require_once 'lib/gamelog.php';

    // this function handles the grunt work of character cleanup.

    // Run any modules hooks who want to deal with character deletion, or stop it
    $return = modulehook('delete_character', ['acctid' => $accountId, 'deltype' => $type, 'dodel' => true]);

    if (! $return['dodel'])
    {
        return false;
    }

    $accountRepository = \Doctrine::getRepository('LotgdCore:Accounts');
    $charRepository = \Doctrine::getRepository('LotgdCore:Characters');
    $mailRepository = \Doctrine::getRepository('LotgdCore:Mail');
    $newsRepository = \Doctrine::getRepository('LotgdCore:News');
    $outputRepository = \Doctrine::getRepository('LotgdCore:AccountsOutput');
    $commentaryRepository = \Doctrine::getRepository('LotgdCore:Commentary');

    $accountEntity = $accountRepository->find($accountId);

    if (! $accountEntity)
    {
        return false;
    }

    // Clean up any clan positions held by this character
    if ($accountEntity->getClanid() && (CLAN_LEADER == $accountEntity->getClanrank() || CLAN_FOUNDER == $accountEntity->getClanrank()))
    {
        //-- Check if clan have more leaders
        $leadersCount = $charRepository->getClanLeadersCount($accountEntity->getClanid());

        if (1 == $leadersCount || 0 == $leadersCount)
        {
            $result = $charRepository->getViableLeaderForClan($accountEntity->getClanid(), $accountId);

            if ($result)
            {
                //-- there is no alternate leader, let's promote the
                //-- highest ranking member (or oldest member in the
                //-- event of a tie).  This will capture even people
                //-- who applied for membership.
                $charRepository->setNewClanLeader($result['id']);

                gamelog("Clan {$accountEntity->getClanid()} has a new leader {$result['id']} as there were no others left", 'clan');
            }
            else
            {
                $clanRepository = \Doctrine::getRepository('LotgdCore:Clans');
                $clanEntity = $clanRepository->find($accountEntity->getClanid());

                //-- There are no other members, we need to delete the clan.
                modulehook('clan-delete', ['clanid' => $accountEntity->getClanid(), 'clanEntity' => $clanEntity]);

                \Doctrine::remove($clanEntity);

                //just in case we goofed, we don't want to have to worry
                //about people being associated with a deleted clan.
                $charRepository->expelPlayersFromDeletedClan($session['user']['clanid']);

                gamelog('Clan '.$session['user']['clanid'].' has been deleted, last member gone', 'Clan');

                unset($clanEntity);
            }
        }
    }

    // Delete any module user prefs
    module_delete_userprefs($accountId);

    // Delete any mail to or from the user
    $mailRepository->deleteMailOfAccount($accountId);

    // Delete any news from the user
    $newsRepository->deleteNewsOfAccount($accountId);

    // delete the output field from the accounts_output table introduced in 1.1.1
    $outputRepository->deleteOutputOfAccount($accountId);

    // delete the comments the user posted, necessary to have the systemcomments with acctid 0 working
    $commentaryRepository->deleteCommentsOfAccount($accountId);

    // delete character of account
    $charRepository->deleteCharacter($accountEntity->getCharacter()->getId());

    // Delete account
    \Doctrine::remove($accountEntity);

    \Doctrine::flush();

    return true;
}
