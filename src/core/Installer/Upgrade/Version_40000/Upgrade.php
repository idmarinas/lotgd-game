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
use Tracy\Debugger;
use Zend\Hydrator\ClassMethods;

class Upgrade extends UpgradeAbstract
{
    /**
     * Step 1 rename old table "commentary" and create new.
     *
     * @return bool
     */
    public function step1(): bool
    {
        try
        {
            $this->messages[] = \LotgdTranslator::t('upgrade.version.to', ['version' => '4.0.0 IDMarinas Edition'], self::TRANSLATOR_DOMAIN);

            $this->connection->exec('RENAME TABLE `commentary` TO `commentary_old`;');

            $this->syncEntity(\Lotgd\Core\Entity\Commentary::class);

            $this->messages[] = \LotgdTranslator::t('upgrade.version.table.rename', ['old' => 'commentary', 'new' => 'commentary_old'], self::TRANSLATOR_DOMAIN);
            $this->messages[] = \LotgdTranslator::t('upgrade.version.table.create', ['table' => 'commentary'], self::TRANSLATOR_DOMAIN);

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Import data of old table.
     *
     * @return bool
     */
    public function step2(): bool
    {
        try
        {
            $hydrator = new ClassMethods();
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
                    $row = array_merge((array)$row, (array) $row['info']);
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

                    $entity = $hydrator->hydrate($row, new \Lotgd\Core\Entity\Commentary());

                    $this->doctrine->persist($entity);
                }

                $this->doctrine->flush();

                $page++;
                $paginator = \DB::paginator($select, $page, 100);
            } while ($paginator->getCurrentItemCount() && $page <= $pageCount);

            $this->messages[] = \LotgdTranslator::t('upgrade.version.table.import', ['count' => $importCount, 'table' => 'commentary'], self::TRANSLATOR_DOMAIN);

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
     * Delete old table if all rows are imported.
     *
     * @return bool
     */
    public function step3(): bool
    {
        try
        {
            $this->connection->exec('DROP TABLE IF EXISTS `commentary_old`;');

            $this->messages[] = \LotgdTranslator::t('upgrade.version.table.delete', ['table' => 'commentary_old'], self::TRANSLATOR_DOMAIN);

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }
}
