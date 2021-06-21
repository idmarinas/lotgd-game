# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/migration/README.md).   
Visit **_latest_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG.md)  
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V3.md)  
Visit **_V4_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V4.md)  

# Version: 5.5.0 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   **Sonata Block Event**
    -   **themes/LotgdTheme/templates/_layout.html.twig** 
        -   Add sonata block event `lotgd_core.paypal`  
            -   Example of usage in `src/core/Service/PaypalButtons` and see service `lotgd_core.service.paypal.buttons` in `config/service.yaml`
    -   Add new Sonata Block Events: 
        -   **themes/LotgdTheme/templates/page/home.html.twig**
            -   `lotgd.core.page.home.pre` avoid using `includeTemplatesPre` in `$params`
            -   `lotgd.core.page.home.index` avoid using `includeTemplatesIndex` in `$params`
            -   `lotgd.core.page.home.text` avoid using `hookHomeText` in `$params`
            -   `lotgd.core.page.home.middle` avoid using `hookHomeMiddle` in `$params`
            -   `lotgd.core.page.home.post` avoid using `includeTemplatesPost` in `$params`
-   **Create service for Buff and TemptStat**
    -   `lib/buffs.php`
    -   `lib/tempstat.php`

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   Nothing

### :x: REMOVES

-   **BC** **Remove script of Cookie Consent** only need in EU territory, if you need can use one of this:
    -   https://github.com/kiprotect/klaro
    -   https://github.com/nucleos/NucleosGDPRBundle
    -   https://github.com/osano/cookieconsent/

### :notebook: NOTES

-   Make some optimizations in files. (Code Smell/Duplicated)
-   **Notes**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies
