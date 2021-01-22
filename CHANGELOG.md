# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/master/README.md).   
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V3.md)  
Visit **_V4_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V4.md)  

# Version: 5.0.0

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   Nothing

### :x: REMOVES/Break Changes

-   Nothing

### :notebook: NOTES

-   **Upgrade/Install for version 4.9.0 and up**
    -   First, upload files to your server (production compilation):
    -   Second, empty cache:
        -   `var/` delete this folder (or use command in console `php bin/console cache:clear`).
            -   From version 4.9.0 use Symfony Kernel, so work like Symfony Framework.
        -   `storage/cache/*` can empty with console comand `php bin/lotgd storage:cache_clear`
            -   Not delete `.gitkeep` files. Remember to keep the main structure of the folder `storage/cache/`
            -   It is highly recommended to use the command  `php bin/lotgd storage:cache_clear` instead delete folder.
            -   Note: if fail when run console command, manual delete: `storage/cache/service-manager.config.php`
    -   Third, read info in `storage/log/tracy/*` files, and see the problem.
    -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies
