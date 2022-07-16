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

use Throwable;
use Tracy\Debugger;
use Lotgd\Core\Entity\User;
use Lotgd\Core\Entity\Avatar;
use Doctrine\ORM\EntityManagerInterface;
use Kit\CryptBundle\Service\OpensslService as Crypt;
use Lotgd\Core\Event\Character as CharacterEvent;
use Lotgd\Core\Event\Clan as ClanEvent;
use Lotgd\Core\Log;
use Lotgd\Core\Repository\RepositoryBackupInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Backup
{
    private $doctrine;
    private $log;
    private $eventDispatcher;
    private $normalizer;
    private $serializer;
    private $crypt;

    public function __construct(
        EntityManagerInterface $doctrine,
        Log $log,
        EventDispatcherInterface $eventDispatcher,
        NormalizerInterface $normalizer,
        SerializerInterface $serializer,
        Crypt $crypt
    ) {
        $this->doctrine        = $doctrine;
        $this->log             = $log;
        $this->eventDispatcher = $eventDispatcher;
        $this->normalizer      = $normalizer; //-- object to array
        $this->serializer      = $serializer;
        $this->crypt           = $crypt;
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
                'LotgdCore:Debuglog'        => true,
            ],
            'encrypt' => [
                // 'Entity:Name' => encrypt: true|false,
                // Same name as in entities
            ],
            'acctid'  => $accountId,
            'deltype' => $type,
        ]);
        $this->eventDispatcher->dispatch($return, CharacterEvent::CLEANUP);
        $return = $return->getData();

        $accountRepository = $this->doctrine->getRepository('LotgdCore:User');
        $accountEntity     = $accountRepository->find($accountId);

        //-- Not do nothing if not find account or fail in create basic backup
        if ( ! $accountEntity || ! $this->createBackupBasicInfo($accountId, $accountEntity, $return['encrypt']))
        {
            $this->log->game("Could not create basic info backup for the account ID:{$accountEntity->getAcctid()}, Login {$accountEntity->getLogin()}, canceled", 'backup');

            return false;
        }

        $backupEntities = $return['entities'];
        unset($backupEntities['LotgdCore:User'], $backupEntities['LotgdCore:Avatar']); //-- Always backup Account and Character

        $accountLogin = $accountEntity->getLogin();
        //-- Backup and delete data from DataBase
        foreach ($backupEntities as $entity => $backup)
        {
            try
            {
                /** @var |Lotgd\Core\Repository\RepositoryBackupInterface $repository */
                $repository = $this->doctrine->getRepository($entity);

                //-- Skip if not have methods for backup table
                if ( ! $repository instanceof RepositoryBackupInterface)
                {
                    continue;
                }

                if ($backup)
                {
                    $message = 'Could not create a backup for Entity %s, Account ID: %s, Login %s';

                    if ($this->createBackupOfEntity($accountId, $repository, $entity, $return['encrypt'][$entity] ?? false))
                    {
                        $message = 'A backup has been created for the Entity %s, account ID: %s, Login %s';
                    }

                    $this->log->game(sprintf($message, $entity, $accountId, $accountLogin), 'backup');
                }

                $repository->backupDeleteDataFromAccount($accountId);
            }
            catch (Throwable $th)
            {
                Debugger::log($th);

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
        /** @var \Lotgd\Core\Repository\AvatarRepository $charRepository */
        $charRepository = $this->doctrine->getRepository('LotgdCore:Avatar');

        if (
            $accountEntity->getAvatar()->getClanid()
            && (CLAN_LEADER == $accountEntity->getAvatar()->getClanrank() || CLAN_FOUNDER == $accountEntity->getAvatar()->getClanrank())
        ) {
            //-- Check if clan have more leaders
            $leadersCount = $charRepository->getClanLeadersCount($accountEntity->getAvatar()->getClanid());

            if (1 == $leadersCount || 0 == $leadersCount)
            {
                $result = $charRepository->getViableLeaderForClan($accountEntity->getAvatar()->getClanid(), $accountId);

                if ($result)
                {
                    //-- there is no alternate leader, let's promote the
                    //-- highest ranking member (or oldest member in the
                    //-- event of a tie).  This will capture even people
                    //-- who applied for membership.
                    $charRepository->setNewClanLeader($result['id']);

                    $this->log->game("Clan {$accountEntity->getAvatar()->getClanid()} has a new leader {$result['id']} as there were no others left", 'clan');
                }
                else
                {
                    $clanRepository = $this->doctrine->getRepository('LotgdCore:Clans');
                    $clanEntity     = $clanRepository->find($accountEntity->getAvatar()->getClanid());

                    //-- There are no other members, we need to delete the clan.
                    $return = new ClanEvent(['clanid' => $accountEntity->getAvatar()->getClanid(), 'clanEntity' => $clanEntity]);
                    $this->eventDispatcher->dispatch($return, ClanEvent::DELETE);

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
    private function createBackupOfEntity(int $accountId, $repository, string $entityName, bool $encrypt = false): bool
    {
        $fileSystem = new Filesystem();

        try
        {
            $rows = $repository->backupGetDataFromAccount($accountId);

            //-- Not save backup if rows are empty
            if (empty($rows))
            {
                return false;
            }

            $callback = $this->circularReferenceHandler();
            $data     = [
                // 'table'  => $this->doctrine->getClassMetadata($entityName)->getTableName(),
                'entity' => $repository->getClassName(),
                'rows'   => $this->normalizer->normalize($rows, null, [
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => $callback,
                ]),
            ];

            $entityName = str_replace([':', '\\', '/'], '_', $entityName);
            $data       = $this->serializer->serialize($data, 'json');

            if ($encrypt)
            {
                $data = $this->crypt->encrypt($data);
            }

            $fileSystem->dumpFile("storage/logd_snapshots/user-{$accountId}/{$entityName}.json", $data);
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return false;
        }

        return true;
    }

    /**
     * Create a basic info backup of account.
     *
     * @param mixed $account
     */
    private function createBackupBasicInfo(int $accountId, $account, array $encrypt): bool
    {
        $fileSystem = new Filesystem();
        $path       = "storage/logd_snapshots/user-{$accountId}";

        try
        {
            $basicInfo = [
                'accountId'   => $accountId,
                'characterId' => $account->getAvatar()->getId(),
                'name'        => $account->getAvatar()->getName(),
                'login'       => $account->getLogin(),
                'email'       => $account->getEmailaddress(),
                'lastIp'      => $account->getLastip(),
                'encrypted'   => $encrypt,
            ];

            $callback   = $this->circularReferenceHandler();
            $accountRow = $this->normalizer->normalize($account, null, [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => $callback,
            ]);
            $characterRow = $accountRow['avatar'];

            $accountArray = [
                // 'table'  => 'user',
                'entity' => User::class,
                'rows'   => [$accountRow],
            ];
            $characterArray = [
                // 'table'  => 'avatar',
                'entity' => Avatar::class,
                'rows'   => [$characterRow],
            ];

            $fileSystem->dumpFile("{$path}/LotgdCore_User.json", $this->crypt->encrypt($this->serializer->serialize($accountArray, 'json')));
            $fileSystem->dumpFile("{$path}/LotgdCore_Avatar.json", $this->serializer->serialize($characterArray, 'json'));

            //-- Basic info of account
            $fileSystem->dumpFile("{$path}/basic_info.json", $this->crypt->encrypt($this->serializer->serialize($basicInfo, 'json')));
        }
        catch (Throwable $th)
        {
            Debugger::log($th);

            return false;
        }

        return true;
    }

    private function circularReferenceHandler()
    {
        return function ($object)
        {
            $property = $this->doctrine->getClassMetadata(\get_class($object))->getSingleIdentifierFieldName();
            $method   = 'get'.ucfirst($property);

            return $object->{$method}();
        };
    }
}
