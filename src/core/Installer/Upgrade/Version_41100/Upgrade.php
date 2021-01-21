<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 4.11.0
 */

namespace Lotgd\Core\Installer\Upgrade\Version_41100;

use Lotgd\Core\Installer\UpgradeAbstract;
use Lotgd\Core\Paginator\Adapter\Doctrine;
use Symfony\Component\Filesystem\Filesystem;
use Tracy\Debugger;

class Upgrade extends UpgradeAbstract
{
    public const VERSION_NUMBER    = 41100;
    public const CONFIG_DIR_GLOBAL = 'config/autoload/global';

    /**
     * Step 1: Remove some files.
     */
    public function step1(): bool
    {
        try
        {
            $files = [
                self::CONFIG_DIR_GLOBAL.'/advertising-lotgd-core.php',
            ];

            try
            {
                $fs = new Filesystem();

                $fs->remove($files);

                $this->messages[] = \LotgdTranslator::t('file.removed', ['file' => '"'.\implode('", "', $files).'"'], self::TRANSLATOR_DOMAIN);
            }
            catch (\Throwable $th)
            {
                $this->messages[] = $th->getMessage();
            }

            return true;
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);

            return false;
        }
    }

    /**
     * Step 2: Update text domain of news table.
     */
    public function step2(): bool
    {
        try
        {
            $repository = $this->doctrine->getRepository('LotgdCore:News');
            $page       = 1;
            $query      = $repository->createQueryBuilder('u');
            $paginator  = $repository->getPaginator($query, $page, 100, Doctrine::HYDRATE_OBJECT);
            $pageCount  = $paginator->count();
            $totalCount = $paginator->getTotalItemCount();

            do
            {
                foreach ($paginator as $entity)
                {
                    if ('' == $entity->getTextDomain())
                    {
                        $data = $entity->getArguments();

                        if (isset($data['deathmessage']))
                        {
                            $data['deathmessage']['textDomain'] = \str_replace('-', '_', $data['deathmessage']['textDomain']);
                        }

                        if (isset($data['taunt']))
                        {
                            $data['taunt']['textDomain'] = \str_replace('-', '_', $data['taunt']['textDomain']);
                        }

                        $entity->setArguments($data);
                    }
                    else
                    {
                        $entity->setTextDomain(\str_replace('-', '_', $entity->getTextDomain()));
                    }

                    $this->doctrine->persist($entity);
                }

                $this->doctrine->flush();

                ++$page;
                $paginator = $repository->getPaginator($query, $page, 100, Doctrine::HYDRATE_OBJECT);
            } while ($paginator->getCurrentItemCount() && $page <= $pageCount);

            $this->messages[] = \LotgdTranslator::t('insertData.data.update', ['count' => $totalCount, 'table' => 'news'], self::TRANSLATOR_DOMAIN);

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
     * Step 3: Update field donationconfig.
     */
    public function step3(): bool
    {
        try
        {
            $query = $this->doctrine->createQueryBuilder();

            $count = $query->update('LotgdCore:Accounts', 'u')
                ->set('donationconfig', ':new')

                ->where('donationconfig = :old')

                ->setParameter('new', 'a:0:{}')
                ->setParameter('old', 's:0:""')

                ->getQuery()
                ->execute()
            ;

            $this->messages[] = \LotgdTranslator::t('insertData.data.update', ['count' => $count, 'table' => 'accounts'], self::TRANSLATOR_DOMAIN);
        }
        catch (\Throwable $th)
        {
            Debugger::log($th);
            $this->messages[] = $th->getMessage();
        }

        return true;
    }
}
