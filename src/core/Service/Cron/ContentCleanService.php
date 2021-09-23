<?php

/**
 * This file is part of Legend of the Green Dragon.
 *
 * @see https://github.com/idmarinas/lotgd-game
 *
 * @license https://github.com/idmarinas/lotgd-game/blob/migration/public/LICENSE.txt
 * @author IDMarinas
 *
 * @since 6.2.0
 */

namespace Lotgd\Core\Service\Cron;

use Doctrine\ORM\EntityManagerInterface;
use Lotgd\Core\Lib\Settings;
use Lotgd\Core\Log;

class ContentCleanService
{
    private $log;
    private $doctrine;
    private $settings;

    public function __construct(Log $log, Settings $settings, EntityManagerInterface $doctrine)
    {
        $this->log = $log;
        $this->doctrine = $doctrine;
        $this->settings = $settings;
    }

    public function execute(): void
    {
        //-- Clean up referer entries
        /** @var \Lotgd\Core\Repository\ReferersRepository $repository */
        $repository = $this->doctrine->getRepository('LotgdCore:Referers');
        $result     = $repository->deleteExpireReferers(60);

        $this->log->game("Deleted {$result} records from referers older than 60 days.", 'maintenance');

        //-- Clean up old mails
        /** @var \Lotgd\Core\Repository\MailRepository $repository */
        $repository = $this->doctrine->getRepository('LotgdCore:Mail');
        $time       = (int) $this->settings->getSetting('oldmail', 14);
        $result     = $repository->deleteExpireMail($time);

        $this->log->game("Deleted {$result} records from mail older than {$time} days.", 'maintenance');

        //-- CONTENT
        $time = (int) $this->settings->getSetting('expirecontent', 180);

        if ($time !== 0)
        {
            //-- Clean up news
            /** @var \Lotgd\Core\Repository\NewsRepository $repository */
            $repository = $this->doctrine->getRepository('LotgdCore:News');
            $result     = $repository->deleteExpireNews($time);

            $this->log->game("Deleted {$result} records from news older than {$time} days.", 'maintenance');

            //-- Clean up old comments
            /** @var \Lotgd\Core\Repository\CommentaryRepository $repository */
            $repository = $this->doctrine->getRepository('LotgdCore:Commentary');
            $result     = $repository->deleteExpireComments($time);

            $this->log->game("Deleted {$result} records from commentary older than {$time} days.", 'comment expiration');

            //-- Expire the faillog entries
            $time = (int) $this->settings->getSetting('expirefaillog', 1);

            if ($time !== 0)
            {
                /** @var \Lotgd\Core\Repository\FaillogRepository $repository */
                $repository = $this->doctrine->getRepository('LotgdCore:Faillog');
                $result     = $repository->deleteExpireFaillogs($time);

                $this->log->game("Deleted {$result} records from faillog older than {$time} days.", 'maintenance');
            }
        }

        //-- Clean up game log
        $time = (int) $this->settings->getSetting('expiregamelog', 30);

        if ($time !== 0)
        {
            /** @var \Lotgd\Core\Repository\GamelogRepository $repository */
            $repository = $this->doctrine->getRepository('LotgdCore:Gamelog');
            $result     = $repository->deleteExpireGamelogs($time);

            $this->log->game("Deleted {$result} records from gamelog older than {$time} days.", 'maintenance');
        }

    }
}
