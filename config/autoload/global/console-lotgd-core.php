<?php

return [
    'console' => [
        'commands' => [
            Lotgd\Core\Command\AboutCommand::class,
            Lotgd\Core\Command\StorageCacheClearCommand::class,
            Lotgd\Core\Command\StorageCacheStatsCommand::class,
        ]
    ]
];
