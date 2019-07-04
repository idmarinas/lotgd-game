<?php

/**
 * Delete an account and create a backup.
 *
 * @param int $accountId
 * @param int $type
 *
 * @return bool
 */
function char_cleanup($accountId, $type): bool
{
    require_once 'lib/gamelog.php';

    // this function handles the grunt work of character cleanup.

    // Run any modules hooks who want to deal with character deletion, or stop it
    $return = modulehook('delete_character', ['acctid' => $accountId, 'deltype' => $type, 'dodel' => true, 'backupAccount' => true]);

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

    //-- Generate a backup.
    if ($return['backupAccount'] && createBackupAccount($accountId))
    {
        gamelog("A backup has been created for the account ID:{$accountEntity->getAcctid()}, Login {$accountEntity->getLogin()}", 'backup');
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

/**
 * Create backup of account in "logd_snapshots".
 *
 * @param int $accountId
 *
 * @return bool
 */
function createBackupAccount(int $accountId): bool
{
    $path = "data/logd_snapshots/account-{$accountId}";

    if (! \file_exists($path))
    {
        mkdir($path, 0777);
    }

    $accountRepository = \Doctrine::getRepository('LotgdCore:Accounts');
    $charRepository = \Doctrine::getRepository('LotgdCore:Characters');
    $mailRepository = \Doctrine::getRepository('LotgdCore:Mail');
    $newsRepository = \Doctrine::getRepository('LotgdCore:News');
    $commentaryRepository = \Doctrine::getRepository('LotgdCore:Commentary');
    $modulePrefsRepository = \Doctrine::getRepository('LotgdCore:ModuleUserprefs');

    try
    {
        $account = $accountRepository->extractEntity($accountRepository->find($accountId));
        $character = $charRepository->extractEntity($charRepository->find($account['character']));
        $mail = $mailRepository->extractEntity($mailRepository->findBy(['msgto' => $accountId]));
        $news = $newsRepository->extractEntity($newsRepository->findBy(['accountId' => $accountId]));
        $commentary = $commentaryRepository->extractEntity($commentaryRepository->findBy(['author' => $accountId]));
        $modulePrefs = $modulePrefsRepository->extractEntity($modulePrefsRepository->findBy(['userid' => $accountId]));
        $account['character'] = $character['id'];
        $character['acct'] = $accountId;

        $basicInfo = [
            'accountId' => $accountId,
            'characterId' => $character['id'],
            'name' => $character['name'],
            'login' => $account['login'],
            'email' => $account['emailaddress'],
            'lastIp' => $account['lastip']
        ];

        //-- Save data of Tables
        //-----------------------
        file_put_contents("{$path}/account.json", json_encode($account), LOCK_EX);
        file_put_contents("{$path}/character.json", json_encode($character), LOCK_EX);
        file_put_contents("{$path}/mail.json", json_encode($mail), LOCK_EX);
        file_put_contents("{$path}/news.json", json_encode($news), LOCK_EX);
        file_put_contents("{$path}/commentary.json", json_encode($commentary), LOCK_EX);
        file_put_contents("{$path}/module_prefs.json", json_encode($modulePrefs), LOCK_EX);

        //-- Basic info of account
        file_put_contents("{$path}/basic_info.json", json_encode($basicInfo), LOCK_EX);
    }
    catch (\Throwable $th)
    {
        \Tracy\Debugger::log($th);

        return false;
    }

    return true;
}
