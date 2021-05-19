<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/master/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.0.0
 */

namespace Lotgd\Bundle\CoreBundle\Installer\Upgrade;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Lotgd\Bundle\CoreBundle\Installer\InstallerAbstract;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

class Version60000 extends InstallerAbstract
{
    protected $upgradeVersion = 60000;
    protected $migration      = 20210506112034;

    private $paginator;

    public function __construct(EntityManagerInterface $doctrine, TranslatorInterface $translator, PaginatorInterface $paginator)
    {
        parent::__construct($doctrine, $translator);

        $this->paginator = $paginator;
    }

    //-- Delete old files
    public function step0()
    {
        $fs = new Filesystem();

        try
        {
            $fs->remove([
                //-- /public old files, now have routes in app
                $this->getProjectDir().'/public/about.php',
                $this->getProjectDir().'/public/account.php',
                $this->getProjectDir().'/public/ajaxmailsearch.php',
                $this->getProjectDir().'/public/armor.php',
                $this->getProjectDir().'/public/armoreditor.php',
                $this->getProjectDir().'/public/badnav.php',
                $this->getProjectDir().'/public/bank.php',
                $this->getProjectDir().'/public/bans.php',
                $this->getProjectDir().'/public/battle.php',
                $this->getProjectDir().'/public/bio.php',
                $this->getProjectDir().'/public/bios.php',
                $this->getProjectDir().'/public/characterbackup.php',
                $this->getProjectDir().'/public/clan.php',
                $this->getProjectDir().'/public/create.php',
                $this->getProjectDir().'/public/companions.php',
                $this->getProjectDir().'/public/configuration.php',
                $this->getProjectDir().'/public/creatures.php',
                $this->getProjectDir().'/public/cronjob.php',
                $this->getProjectDir().'/public/debug.php',
                $this->getProjectDir().'/public/donators.php',
                $this->getProjectDir().'/public/dragon.php',
                $this->getProjectDir().'/public/forest.php',
                $this->getProjectDir().'/public/gamelog.php',
                $this->getProjectDir().'/public/gardens.php',
                $this->getProjectDir().'/public/globaluserfuntions.php',
                $this->getProjectDir().'/public/graveyard.php',
                $this->getProjectDir().'/public/gypsy.php',
                $this->getProjectDir().'/public/healer.php',
                $this->getProjectDir().'/public/hof.php',
                $this->getProjectDir().'/public/home.php', //-- index.php is the entry to the app
                $this->getProjectDir().'/public/inn.php',
                $this->getProjectDir().'/public/list.php',
                $this->getProjectDir().'/public/lodge.php',
                $this->getProjectDir().'/public/logdnet.php',
                $this->getProjectDir().'/public/masters.php',
                $this->getProjectDir().'/public/mercenarycamp.php',
                $this->getProjectDir().'/public/moderate.php',
                $this->getProjectDir().'/public/mounts.php',
                $this->getProjectDir().'/public/newday.php',
                $this->getProjectDir().'/public/news.php',
                $this->getProjectDir().'/public/paylog.php',
                $this->getProjectDir().'/public/payment.php',
                $this->getProjectDir().'/public/prefs.php',
                $this->getProjectDir().'/public/pvp.php',
                $this->getProjectDir().'/public/rawsql.php',
                $this->getProjectDir().'/public/referers.php',
                $this->getProjectDir().'/public/referral.php',
                $this->getProjectDir().'/public/rock.php',
                $this->getProjectDir().'/public/shades.php',
                $this->getProjectDir().'/public/stables.php',
                $this->getProjectDir().'/public/stats.php',
                $this->getProjectDir().'/public/superuser.php',
                $this->getProjectDir().'/public/titleedit.php',
                $this->getProjectDir().'/public/train.php',
                $this->getProjectDir().'/public/user.php',
                $this->getProjectDir().'/public/viewpetition.php',
                $this->getProjectDir().'/public/village.php',
                $this->getProjectDir().'/public/weaponeditor.php',
                $this->getProjectDir().'/public/weapons.php',
                $this->getProjectDir().'/public/whostyping.php',
                //-- Files in this directories are moved to /src/Bundle or deleted
                $this->getProjectDir().'/src/ajax/',
                $this->getProjectDir().'/src/core/',
                $this->getProjectDir().'/src/local/',
                //-- Other files/folder
                $this->getProjectDir().'/cronjob/', //-- Now use bin/console for crons
                $this->getProjectDir().'/modules/', //-- Now use Bundle system
                $this->getProjectDir().'/storage/log/',
                $this->getProjectDir().'/templates_modules/', //-- Now use Bundle system

            ]);
        }
        catch (\Throwable $th)
        {
            return false;
        }

        return true;
    }

    //-- Insert data of settings domains.
    public function step1(): bool
    {
        try
        {
            $sql = \file_get_contents(__DIR__.'/data/60000/setting_domain.sql');

            $this->doctrine->getConnection()->executeQuery($sql);
        }
        catch (\Throwable $th)
        {
            return false;
        }

        return true;
    }

    /**
     * Step 2: Import commentary to new commentary system.
     */
    public function step2(): bool
    {
        try
        {
            $page = 1;
            /** @var \Doctrine\ORM\EntityRepository */
            $repository = $this->doctrine->getRepository('LotgdCommentary:Comment');
            $query      = $repository->createQueryBuilder('u');
            $pagination = $this->paginator->paginate($query, $page, 100);
            $pageCount  = (int) \ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage());

            do
            {
                foreach ($pagination as $entity)
                {
                    if (
                        $entity->getRawBody()
                        || empty($entity->getParams())
                        || ! isset($entity->getParams()['rawcomment'])
                        || ! $entity->getParams()['rawcomment']
                    ) {
                        continue;
                    }

                    $extra = $entity->getParams();

                    $entity->setRawBody($extra['rawcomment']);

                    unset($extra['rawcomment']);

                    $entity->setParams($extra);

                    $this->doctrine->persist($entity);
                }

                $this->doctrine->flush();

                ++$page;
                $pagination = $this->paginator->paginate($query, $page, 100);
            } while ($pagination->count() && $page <= $pageCount);

            return true;
        }
        catch (\Throwable $th)
        {
            return false;
        }
    }
}
