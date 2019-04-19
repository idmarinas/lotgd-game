<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.0.0
 */

namespace Lotgd\Core\Installer\Upgrade\Version_40000;

use Lotgd\Core\Installer\UpgradeAbstract;
use Lotgd\Core\Output\Commentary;
use Tracy\Debugger;
use Zend\Hydrator\ClassMethods;

class Upgrade extends UpgradeAbstract
{
    const VERSION_NUMBER = 40000;

    /**
     * Step 1: Rename tables "accounts" and "commentary" and create new tables.
     *
     * @return bool
     */
    public function step1(): bool
    {
        try
        {
            $this->messages[] = \LotgdTranslator::t('upgrade.version.to', ['version' => '4.0.0 IDMarinas Edition'], self::TRANSLATOR_DOMAIN);

            $this->connection->exec('RENAME TABLE `commentary` TO `commentary_old`;');
            $this->connection->exec('RENAME TABLE `accounts` TO `accounts_old`;');

            $this->messages[] = \LotgdTranslator::t(self::TRANSLATOR_KEY_TABLE_RENAME, ['old' => 'commentary', 'new' => 'commentary_old'], self::TRANSLATOR_DOMAIN);
            $this->messages[] = \LotgdTranslator::t(self::TRANSLATOR_KEY_TABLE_RENAME, ['old' => 'accounts', 'new' => 'accounts_old'], self::TRANSLATOR_DOMAIN);

            $this->syncEntity(\Lotgd\Core\Entity\Commentary::class);
            $this->syncEntity(\Lotgd\Core\Entity\Accounts::class);
            $this->syncEntity(\Lotgd\Core\Entity\Characters::class);

            $this->messages[] = \LotgdTranslator::t(self::TRANSLATOR_KEY_TABLE_CREATE, ['table' => 'commentary'], self::TRANSLATOR_DOMAIN);
            $this->messages[] = \LotgdTranslator::t(self::TRANSLATOR_KEY_TABLE_CREATE, ['table' => 'accounts'], self::TRANSLATOR_DOMAIN);
            $this->messages[] = \LotgdTranslator::t(self::TRANSLATOR_KEY_TABLE_CREATE, ['table' => 'characters'], self::TRANSLATOR_DOMAIN);

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Import data of old "commentary" table.
     *
     * @return bool
     */
    public function step2(): bool
    {
        try
        {
            $hydrator = new ClassMethods();
            $commentary = new Commentary();
            $page = 1;
            $select = \DB::select('commentary_old');
            $paginator = \DB::paginator($select, $page, 100);

            $pageCount = $paginator->count();
            $importCount = $paginator->getTotalItemCount();

            do
            {
                foreach ($paginator as $row)
                {
                    $row['info'] = unserialize($row['info']);
                    $row = array_merge((array) $row, (array) $row['info']);
                    $row['postdate'] = new \DateTime($row['postdate']);

                    $row['id'] = (int) $row['commentid'];
                    $row['extra'] = $row['info'];
                    $row['authorName'] = (string) $row['name'];
                    $row['clanName'] = (string) ($row['clanname'] ?? '');
                    $row['clanNameShort'] = (string) ($row['clanshort'] ?? '');
                    $row['clanRank'] = (int) ($row['clanrank'] ?? '');
                    $row['clanId'] = (int) ($row['clanid'] ?? '');
                    $row['hidden'] = (int) ($row['hidecomment'] ?? '');
                    $row['hiddenComment'] = (string) ($row['hidereason'] ?? '');
                    $row['hiddenBy'] = (string) ($row['hiddenby'] ?? '');

                    $commentary->processSpecialCommands($row);

                    $entity = $hydrator->hydrate($row, new \Lotgd\Core\Entity\Commentary());

                    $this->doctrine->persist($entity);
                }

                $this->doctrine->flush();

                $page++;
                $paginator = \DB::paginator($select, $page, 100);
            } while ($paginator->getCurrentItemCount() && $page <= $pageCount);

            $this->messages[] = \LotgdTranslator::t(self::TRANSLATOR_KEY_TABLE_IMPORT, ['count' => $importCount, 'table' => 'commentary'], self::TRANSLATOR_DOMAIN);

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $this->messages[] = $th->getMessage();

            return false;
        }
    }

    /**
     * Import data of old "accounts" table.
     * This table are splited in two tables: "accounts" and "characters".
     *
     * @return bool
     */
    public function step3(): bool
    {
        try
        {
            $hydrator = new ClassMethods();
            $page = 1;
            $select = \DB::select('accounts_old');
            $paginator = \DB::paginator($select, $page, 100);

            $pageCount = $paginator->count();
            $importCount = $paginator->getTotalItemCount();

            //-- Overrides the automatic generation of IDs in this query to avoid changing IDs of accounts.
            $metadataAcct = $this->doctrine->getClassMetadata(\Lotgd\Core\Entity\Accounts::class);
            $metadataAcct->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
            $metadataAcct->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

            do
            {
                foreach ($paginator as $row)
                {
                    $row = (array) $row;
                    $row['laston'] = new \DateTime($row['laston']);
                    $row['lastmotd'] = new \DateTime($row['lastmotd']);
                    $row['lasthit'] = new \DateTime($row['lasthit']);
                    $row['pvpflag'] = new \DateTime($row['pvpflag']);
                    $row['recentcomments'] = new \DateTime($row['recentcomments']);
                    $row['biotime'] = new \DateTime($row['biotime']);
                    $row['regdate'] = new \DateTime($row['regdate']);
                    $row['clanjoindate'] = new \DateTime($row['clanjoindate']);
                    $row['badguy'] = unserialize($row['badguy']);
                    $row['companions'] = unserialize($row['companions']);
                    $row['allowednavs'] = unserialize($row['allowednavs']);
                    $row['bufflist'] = unserialize($row['bufflist']);
                    $row['dragonpoints'] = unserialize($row['dragonpoints']);
                    $row['prefs'] = unserialize($row['prefs']);

                    //-- Configure account
                    $acctEntity = $hydrator->hydrate($row, new \Lotgd\Core\Entity\Accounts());

                    $this->doctrine->persist($acctEntity);

                    //-- Configure character
                    $charEntity = $hydrator->hydrate($row, new \Lotgd\Core\Entity\Characters());
                    $charEntity->setAcct($acctEntity);

                    //-- Need for get ID of new character
                    $this->doctrine->persist($charEntity);
                    $this->doctrine->flush(); //-- Persist objects

                    //-- Set ID of character and update Account
                    $acctEntity->setCharacter($charEntity);

                    $this->doctrine->persist($acctEntity);
                    // $this->doctrine->flush(); //-- Persist objects
                }

                $this->doctrine->flush();

                $page++;
                $paginator = \DB::paginator($select, $page, 100);
            } while ($paginator->getCurrentItemCount() && $page <= $pageCount);

            $metadataAcct->setIdGenerator(new \Doctrine\ORM\Id\IdentityGenerator());
            $metadataAcct->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_IDENTITY);

            $this->messages[] = \LotgdTranslator::t(self::TRANSLATOR_KEY_TABLE_IMPORT, ['count' => $importCount, 'table' => 'accounts'], self::TRANSLATOR_DOMAIN);
            $this->messages[] = \LotgdTranslator::t(self::TRANSLATOR_KEY_TABLE_IMPORT, ['count' => $importCount, 'table' => 'characters'], self::TRANSLATOR_DOMAIN);

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            $this->messages[] = $th->getMessage();

            return false;
        }
    }

    /**
     * Insert new data for this upgrade.
     *
     * @return bool
     */
    public function step4(): bool
    {
        try
        {
            return $this->insertData(self::VERSION_NUMBER);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Delete old table when all rows are imported.
     *
     * @return bool
     */
    public function step5(): bool
    {
        try
        {
            $this->connection->exec('DROP TABLE IF EXISTS `commentary_old`;');
            $this->connection->exec('DROP TABLE IF EXISTS `accounts_old`;');

            $this->messages[] = \LotgdTranslator::t(self::TRANSLATOR_KEY_TABLE_DELETE, ['table' => 'commentary_old'], self::TRANSLATOR_DOMAIN);
            $this->messages[] = \LotgdTranslator::t(self::TRANSLATOR_KEY_TABLE_DELETE, ['table' => 'accounts_old'], self::TRANSLATOR_DOMAIN);

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }
}
