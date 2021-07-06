<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 *
 * @since 5.3.0
 */

namespace Lotgd\Core\Tool;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Serializer\Adapter\PhpSerialize;
use Lotgd\Core\Event\Character as CharacterEvent;
use Lotgd\Core\Event\Clan as ClanEvent;
use Lotgd\Core\Log;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Backup
{
    private $doctrine;
    private $log;
    private $eventDispatcher;

    public function __construct(EntityManagerInterface $doctrine, Log $log, EventDispatcherInterface $eventDispatcher)
    {
        $this->doctrine        = $doctrine;
        $this->log             = $log;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Delete an account and create a backup.
     *
     * In order to create a backup and delete the data, the Entity Repository of each table needs to have the following two methods:
     *  - public function backupDeleteDataFromAccount(int $accountId): array {}
     *  - public function backupGetDataFromAccount(int $accountId): int {}
     */
    public function characterCleanUp(int $accountId, string $type): bool
    {
        // this function handles the grunt work of character cleanup.
        // Run any modules hooks who want to deal with character deletion
        $return = new CharacterEvent([
            'entities' => [
                //-- Delete data from DataBase of all entities here
                // 'Entity:Name' => Backup: true|false,
                'LotgdCore:Mail'            => true,
                'LotgdCore:News'            => true,
                'LotgdCore:AccountsOutput'  => false, //-- The data is not backed up, but it is deleted.
                'LotgdCore:Commentary'      => true,
                'LotgdCore:ModuleUserprefs' => true,
            ],
            'acctid'  => $accountId,
            'deltype' => $type,
        ]);
        $this->eventDispatcher->dispatch($return, CharacterEvent::CLEANUP);
        $return = modulehook('character-cleanup', $return->getData());

        $accountRepository = $this->doctrine->getRepository('LotgdCore:User');
        $accountEntity     = $accountRepository->find($accountId);

        //-- Not do nothing if not find account or fail in create basic backup
        if ( ! $accountEntity || ! $this->createBackupBasicInfo($accountId, $accountEntity))
        {
            $this->log->game("Could not create basic info backup for the account ID:{$accountEntity->getAcctid()}, Login {$accountEntity->getLogin()}, canceled", 'backup');

            return false;
        }

        $backupEntities = $return['entities'];
        unset($backupEntities['LotgdCore:User'], $backupEntities['LotgdCore:Characters']); //-- Always backup Account and Character

        $accountLogin = $accountEntity->getLogin();
        //-- Backup and delete data from DataBase
        foreach ($backupEntities as $entity => $backup)
        {
            try
            {
                /** @var Repository with backup option */
                $repository = $this->doctrine->getRepository($entity);

                //-- Skip if method for deleting data are not found
                if ( ! \method_exists($repository, 'backupDeleteDataFromAccount'))
                {
                    continue;
                }

                if ($backup)
                {
                    $message = 'Could not create a backup for Entity %s, Account ID: %s, Login %s';

                    if ($this->createBackupOfEntity($accountId, $repository, $entity))
                    {
                        $message = 'A backup has been created for the Entity %s, account ID: %s, Login %s';
                    }

                    $this->log->game(\sprintf($message, $entity, $accountId, $accountLogin), 'backup');
                }

                $repository->backupDeleteDataFromAccount($accountId);
            }
            catch (\Throwable $th)
            {
                \Tracy\Debugger::log($th);

                continue;
            }
        }

        // Clean up any clan positions held by this character
        $this->processClan($accountId, $accountEntity);

        //-- Delete character of account
        $this->doctrine->remove($accountEntity->getCharacter());

        //-- Delete account
        $this->doctrine->remove($accountEntity);

        $this->doctrine->flush();

        return true;
    }

    private function processClan(int $accountId, $accountEntity): void
    {
        /** @var \Lotgd\Core\Repository\CharactersRepository */
        $charRepository = $this->doctrine->getRepository('LotgdCore:Characters');

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

                    $this->log->game("Clan {$accountEntity->getCharacter()->getClanid()} has a new leader {$result['id']} as there were no others left", 'clan');
                }
                else
                {
                    $clanRepository = $this->doctrine->getRepository('LotgdCore:Clans');
                    $clanEntity     = $clanRepository->find($accountEntity->getCharacter()->getClanid());

                    //-- There are no other members, we need to delete the clan.
                    $return = new ClanEvent(['clanid' => $accountEntity->getCharacter()->getClanid(), 'clanEntity' => $clanEntity]);
                    $this->eventDispatcher->dispatch($return, ClanEvent::DELETE);
                    modulehook('clan-delete', $return->getData());

                    $this->doctrine->remove($clanEntity);

                    //just in case we goofed, we don't want to have to worry
                    //about people being associated with a deleted clan.
                    $charRepository->expelPlayersFromDeletedClan($clanEntity->getClanid());

                    $this->log->game('Clan '.$clanEntity->getClanid().' has been deleted, last member gone', 'Clan');

                    unset($clanEntity);
                }
            }
        }
    }

    /**
     * Create backup of Entity in "logd_snapshots".
     *
     * @param object $repository
     */
    private function createBackupOfEntity(int $accountId, $repository, string $entityName): bool
    {
        //-- Skip if method for getting data are not found
        if ( ! \method_exists($repository, 'backupGetDataFromAccount'))
        {
            return false;
        }

        $fileSystem = new Filesystem();
        $serializer = new PhpSerialize();

        try
        {
            $data = [
                'table'  => $this->doctrine->getClassMetadata($entityName)->getTableName(),
                'entity' => $repository->getClassName(),
                'rows'   => $repository->extractEntity($repository->backupGetDataFromAccount($accountId)),
            ];
            $entityName = \str_replace([':', '\\', '/'], '_', $entityName);
            $fileSystem->dumpFile("storage/logd_snapshots/account-{$accountId}/{$entityName}.data", $serializer->serialize($data), LOCK_EX);
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
     * @param mixed $account
     */
    private function createBackupBasicInfo(int $accountId, $account): bool
    {
        $fileSystem = new Filesystem();
        $serializer = new PhpSerialize();
        $hydrator   = new \Laminas\Hydrator\ClassMethodsHydrator();
        $hydrator->removeNamingStrategy(); //-- With this keyValue is keyValue. Otherwise it would be key_value
        $path = "storage/logd_snapshots/account-{$accountId}";

        try
        {
            $basicInfo = [
                'accountId'   => $accountId,
                'characterId' => $account->getCharacter()->getId(),
                'name'        => $account->getCharacter()->getName(),
                'login'       => $account->getLogin(),
                'email'       => $account->getEmailaddress(),
                'lastIp'      => $account->getLastip(),
            ];

            $accountRow   = $hydrator->extract($account);
            $characterRow = $hydrator->extract($account->getCharacter());

            $accountArray = [
                'table'  => 'accounts',
                'entity' => \Lotgd\Core\Entity\Accounts::class,
                'rows'   => [$accountRow],
            ];
            $characterArray = [
                'table'  => 'characters',
                'entity' => \Lotgd\Core\Entity\Characters::class,
                'rows'   => [$characterRow],
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
}
