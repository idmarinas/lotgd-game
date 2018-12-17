<?php

return [
    'service_manager' => [
        'factories' => [
            Lotgd\Core\Character\Stats::class => Lotgd\Core\Factory\Character\Stats::class,
            'Lotgd\\Core\\Installer\\Install' => Lotgd\Core\Factory\Installer\Install::class,
            Lotgd\Core\Lib\Cache::class => Lotgd\Core\Factory\Lib\Cache::class,
            Lotgd\Core\Lib\Dbwrapper::class => Lotgd\Core\Factory\Lib\Dbwrapper::class,
            'Lotgd\\Core\\Lib\\Doctrine' => Lotgd\Core\Factory\Lib\Doctrine::class,
            Lotgd\Core\Lib\Settings::class => Lotgd\Core\Factory\Lib\Settings::class,
            Lotgd\Core\Lib\SettingsExtended::class => Lotgd\Core\Factory\Lib\SettingsExtended::class,
            Lotgd\Core\Nav\Blocked::class => Lotgd\Core\Factory\Nav\Blocked::class,
            Lotgd\Core\Output\Collector::class => Lotgd\Core\Factory\Output\Collector::class,
            Lotgd\Core\Output\Color::class => Lotgd\Core\Factory\Output\Color::class,
            Lotgd\Core\Output\Code::class => Lotgd\Core\Factory\Output\Code::class,
            Lotgd\Core\Template\Theme::class => Lotgd\Core\Factory\Template\Theme::class,
            Lotgd\Core\Http::class => Lotgd\Core\Factory\Http::class,
        ]
    ]
];
