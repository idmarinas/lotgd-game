<?php

    //- Clean up referer entries
    $repository = \Doctrine::getRepository('LotgdCore:Referers');
    $result     = $repository->deleteExpireReferers(60);

    \LotgdLog::game("Deleted {$result} records from referers older than 60 days.", 'maintenance');

    //Clean up old mails

    $repository = \Doctrine::getRepository('LotgdCore:Mail');
    $time       = (int) LotgdSetting::getSetting('oldmail', 14);
    $result     = $repository->deleteExpireMail($time);

    \LotgdLog::game("Deleted {$result} records from mail older than {$time} days.", 'maintenance');

    //CONTENT

    $time = (int) LotgdSetting::getSetting('expirecontent', 180);

    if ($time)
    {
        //-- Clean up news
        $repository = \Doctrine::getRepository('LotgdCore:News');
        $result     = $repository->deleteExpireNews($time);

        \LotgdLog::game("Deleted {$result} records from news older than {$time} days.", 'maintenance');

        //-- Clean up old comments
        $repository = \Doctrine::getRepository('LotgdCore:Commentary');
        $result     = $repository->deleteExpireComments($time);

        \LotgdLog::game("Deleted {$result} records from commentary older than {$time} days.", 'comment expiration');

        //-- Expire the faillog entries
        $time = (int) LotgdSetting::getSetting('expirefaillog', 1);

        if ($time)
        {
            $repository = \Doctrine::getRepository('LotgdCore:Faillog');
            $result     = $repository->deleteExpireFaillogs($time);

            \LotgdLog::game("Deleted {$result} records from faillog older than {$time} days.", 'maintenance');
        }
    }

    //-- Clean up game log
    $time = (int) LotgdSetting::getSetting('expiregamelog', 30);

    if ($time)
    {
        $repository = \Doctrine::getRepository('LotgdCore:Gamelog');
        $result     = $repository->deleteExpireGamelogs($time);

        \LotgdLog::game("Deleted {$result} records from gamelog older than {$time} days.", 'maintenance');
    }
