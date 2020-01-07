<?php
    require_once 'lib/gamelog.php';

    //- Clean up referer entries
    $repository = \Doctrine::getRepository('LotgdCore:Referers');
    $result = $repository->deleteExpireReferers(60);

    gamelog("Deleted {$result} records from referers older than 60 days.", 'maintenance');

    //Clean up old mails

    $repository = \Doctrine::getRepository('LotgdCore:Mail');
    $time = (int) getsetting('oldmail', 14);
    $result = $repository->deleteExpireMail($time);

    gamelog("Deleted {$result} records from mail older than {$time} days.", 'maintenance');
    LotgdCache::clearByPrefix('mail');

    //CONTENT

    $time = (int) getsetting('expirecontent', 180);
    if ($time)
    {
        //-- Clean up news
        $repository = \Doctrine::getRepository('LotgdCore:News');
        $result = $repository->deleteExpireNews($time);

        gamelog("Deleted {$result} records from news older than {$time} days.", 'maintenance');

        //-- Clean up old comments
        $repository = \Doctrine::getRepository('LotgdCore:Commentary');
        $result = $repository->deleteExpireComments($time);

        gamelog("Deleted {$result} records from commentary older than {$time} days.", 'comment expiration');

        //-- Expire the faillog entries
        $time = (int) getsetting('expirefaillog', 1);
        if ($time)
        {
            $repository = \Doctrine::getRepository('LotgdCore:Faillog');
            $result = $repository->deleteExpireFaillogs($time);

            gamelog("Deleted {$result} records from faillog older than {$time} days.", 'maintenance');
        }
    }

    //-- Clean up game log
    $time = (int) getsetting('expiregamelog', 30);
    if ($time)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Gamelog');
        $result = $repository->deleteExpireGamelogs($time);

        gamelog("Deleted {$result} records from gamelog older than {$time} days.", 'maintenance');
    }
