<?php

use Lotgd\Core\Service\Cron\ContentCleanService;

LotgdKernel::get(ContentCleanService::class)->execute();
