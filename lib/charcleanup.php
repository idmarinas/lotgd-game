<?php

/**
 * Delete an account and create a backup.
 *
 * In order to create a backup and delete the data, the EntityRepository of each table needs to have the following two methods:
 *  - public function backupDeleteDataFromAccount(int $accountId): array {}
 *  - public function backupGetDataFromAccount(int $accountId): int {}
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
    // Run any modules hooks who want to deal with character deletion
    $return = modulehook('character-cleanup', [
        'entities' => [
            //-- Delete data from DataBase of all entities here
            // 'Entity:Name' => Backup: true|false,
            'LotgdCore:Mail' => true,
            'LotgdCore:News' => true,
            'LotgdCore:AccountsOutput' => false, //-- The data is not backed up, but it is deleted.
            'LotgdCore:Commentary' => true,
            'LotgdCore:ModuleUserprefs' => true
        ],
        'acctid' => $accountId,
        'deltype' => $type
    ]);

    $accountRepository = \Doctrine::getRepository('LotgdCore:Accounts');
    $accountEntity = $accountRepository->find($accountId);

    //-- Not do nothing if not find account or fail in create basic backup
    if (! $accountEntity || ! createBackupBasicInfo($accountId, $accountEntity))
    {
        gamelog("Could not create basic info backup for the account ID:{$accountEntity->getAcctid()}, Login {$accountEntity->getLogin()}, canceled", 'backup');

        return false;
    }

    $backupEntities = $return['entities'];
    unset($backupEntities['LotgdCore:Accounts'], $backupEntities['LotgdCore:Characters']); //-- Always backup Account and Character

    $accountLogin = $accountEntity->getLogin();
    //-- Backup and delete data from DataBase
    foreach ($backupEntities as $entity => $backup)
    {
        try
        {
            $repository = \Doctrine::getRepository($entity);

            //-- Skip if method for deleting data are not found
            if (! \method_exists($repository, 'backupDeleteDataFromAccount'))
            {
                continue;
            }

            if ($backup)
            {
                $message = 'Could not create a backup for Entity %s, Account ID: %s, Login %s';

                if (createBackupOfEntity($accountId, $repository, $entity))
                {
                    $message = 'A backup has been created for the Entity %s, account ID: %s, Login %s';
                }

                gamelog(sprintf($message, $entity, $accountId, $accountLogin), 'backup');
            }

            $repository->backupDeleteDataFromAccount($accountId);
        }
        catch (\Throwable $th)
        {
            \Tracy\Debugger::log($th);

            continue;
        }
    }

    $charRepository = \Doctrine::getRepository('LotgdCore:Characters');
    // Clean up any clan positions held by this character
    if ($accountEntity->getCharacter()->getClanid() && (CLAN_LEADER == $accountEntity->getCharacter()->getClanrank() || CLAN_FOUNDER == $accountEntity->getCharacter()->getClanrank()))
    {
        //-- Check if clan have more leaders
        $leadersCount = $charRepository->getClanLeadersCount($accountEntity->getCharacter()->getClanid());

        if (1 == $leadersCount || 0 == $leadersCount)
        {
            $result = $charRepository->getViableLeaderForClan($accountEntity->getCharacter()->getClanid(), $accountId);

            if ($result)
            {
                //-- there is no alternate leader, let's promote the
                //-- highest ranking member (or oldest member in the
                //-- event of a tie).  This will capture even people
                //-- who applied for membership.
                $charRepository->setNewClanLeader($result['id']);

                gamelog("Clan {$accountEntity->getCharacter()->getClanid()} has a new leader {$result['id']} as there were no others left", 'clan');
            }
            else
            {
                $clanRepository = \Doctrine::getRepository('LotgdCore:Clans');
                $clanEntity = $clanRepository->find($accountEntity->getCharacter()->getClanid());

                //-- There are no other members, we need to delete the clan.
                modulehook('clan-delete', ['clanid' => $accountEntity->getCharacter()->getClanid(), 'clanEntity' => $clanEntity]);

                \Doctrine::remove($clanEntity);

                //just in case we goofed, we don't want to have to worry
                //about people being associated with a deleted clan.
                $charRepository->expelPlayersFromDeletedClan($session['user']['clanid']);

                gamelog('Clan '.$session['user']['clanid'].' has been deleted, last member gone', 'Clan');

                unset($clanEntity);
            }
        }
    }

    //-- Delete character of account
    \Doctrine::remove($accountEntity->getCharacter());

    //-- Delete account
    \Doctrine::remove($accountEntity);

    \Doctrine::flush();

    return true;
}

/**
 * Create backup of Entity in "logd_snapshots".
 *
 * @param int    $accountId
 * @param object $repository
 * @param string $entityName
 *
 * @return bool
 */
function createBackupOfEntity(int $accountId, $repository, string $entityName): bool
{
    //-- Skip if method for getting data are not found
    if (! \method_exists($repository, 'backupGetDataFromAccount'))
    {
        return false;
    }

    $fileSystem = new \Lotgd\Core\Component\Filesystem();
    $serializer = new Zend\Serializer\Adapter\PhpSerialize();

    try
    {
        $data = [
            'table' => \Doctrine::getClassMetadata($entityName)->getTableName(),
            'entity' => $repository->getClassName(),
            'rows' => $repository->extractEntity($repository->backupGetDataFromAccount($accountId))
        ];
        $entityName = str_replace([':', '\\', '/'], '_', $entityName);
        $fileSystem->dumpFile("data/logd_snapshots/account-{$accountId}/{$entityName}.data", $serializer->serialize($data), LOCK_EX);
    }
    catch (\Throwable $th)
    {
        \Tracy\Debugger::log($th);

        return false;
    }

    return true;
}

/**
 * Create a basic info backup of account.
 *
 * @return bool
 */
function createBackupBasicInfo(int $accountId, $account): bool
{
    $fileSystem = new \Lotgd\Core\Component\Filesystem();
    $serializer = new Zend\Serializer\Adapter\PhpSerialize();
    $hydrator = new \Zend\Hydrator\ClassMethods();
    $hydrator->removeNamingStrategy(); //-- With this keyValue is keyValue. Otherwise it would be key_value
    $path = "data/logd_snapshots/account-{$accountId}";

    try
    {
        $basicInfo = [
            'accountId' => $accountId,
            'characterId' => $account->getCharacter()->getId(),
            'name' => $account->getCharacter()->getName(),
            'login' => $account->getLogin(),
            'email' => $account->getEmailaddress(),
            'lastIp' => $account->getLastip()
        ];

        $accountRow = $hydrator->extract($account);
        $characterRow = $hydrator->extract($account->getCharacter());

        $accountArray = [
            'table' => 'accounts',
            'entity' => \Lotgd\Core\Entity\Accounts::class,
            'rows' => [$accountRow]
        ];
        $characterArray = [
            'table' => 'characters',
            'entity' => \Lotgd\Core\Entity\Characters::class,
            'rows' => [$characterRow]
        ];

        $fileSystem->dumpFile("{$path}/LotgdCore_Accounts.data", $serializer->serialize($accountArray));
        $fileSystem->dumpFile("{$path}/LotgdCore_Characters.data", $serializer->serialize($characterArray));

        //-- Basic info of account
        $fileSystem->dumpFile("{$path}/basic_info.data", $serializer->serialize($basicInfo));
    }
    catch (\Throwable $th)
    {
        \Tracy\Debugger::log($th);

        return false;
    }

    return true;
}
