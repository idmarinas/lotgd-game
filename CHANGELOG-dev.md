# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/migration/README.md).  
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V3.md)  
Visit **_V4_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V4.md)  
Visit **_V5_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V5.md)  
Visit **_V6_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V6.md)  
Visit **_V7_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V7.md)  
Visit **_V8_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V8.md)  

# Version: 8.0.0

### :cyclone: CHANGES

-   `src/ajax/core/Mounts.php` migrate this class to Stimulus controller
    -   `src/core/Controller/MountsController.php` use `remote-modal` Stimulus controller to load this.
-   `src/ajax/core/Bans.php` migrate this class to Stimulus controller
    -   `src/core/Controller/BansController.php` use `remote-modal` Stimulus controller to load this.
-   **Change CronJob System**
    -   Need update your crontab:
        -   From `* * * * * cd /path/to/project/public && php cronjob.php 1>> /dev/null 2>&1`  
            To `* * * * * php /path/to/project/bin/console cron:run 1>> /dev/null 2>&1`
    -   Alternative method to run your cronjobs if don't have a dedicated dron daemon:
        ```bash
            bin/console cron:start # will run in background mode, use --blocking to run in foreground
            bin/console cron:stop # will stop the background cron daemon
        ```
    -   More info in [Cron Symfony Bundle](https://github.com/Cron/Symfony-Bundle)

### :star: FEATURES

-   `assets/stimulus/constrollers/remote_modal_controller.js` Add new function `queryParameters` this add query parameters to url
-   **New** `src/core/Tool/LotgdMail.php`
    -   Can use this tool for send email.

### :fire: DEPRECATED

-   `src/core/Kernel.php` Mark const `VERSION_NUMBER` as deprecated
-   **Constants**
    -   `MODULE_NO_INFO`
    -   `MODULE_INSTALLED`
    -   `MODULE_VERSION_OK`
    -   `MODULE_NOT_INSTALLED`
    -   `MODULE_FILE_NOT_PRESENT`
    -   `MODULE_VERSION_TOO_LOW`
    -   `MODULE_ACTIVE`
    -   `MODULE_INJECTED`

### :wrench: FIXES

-   `assets/lib/game/previewfield.js` Updated for use classes of TailwindCSS

### :x: REMOVES

-   **BC** Removed unused code
    -  `assets/lib/components/embed.js`
    -  `assets/lib/components/modal-form.js`
    -  `assets/lib/components/modal.js`
    -  `assets/lib/components/redirect-post.js`
       -  This components use old Fomantic UI
-   **BC** Deleted old modules system. Use bundles instead.
-   **BC** Deleted JaxonPHP from core.
    -   This include all relation code of JaxonPHP
    -   *Note:* If you want to use its capabilities you will have to add the package yourself.
-   **BC** Deleted files:
    -   `lib/showform.php`
    -   `lib/showtabs.php`
        -  *Note:* use Symfony Forms for build forms.
    -   `lib/lotgd_mail.php` Use Symfony mailer or `Lotgd\Core\Tool\LotgdMail`
-   `src/ajax/core/Mounts.php` deleted, now is a Stimulus controller
-   `src/ajax/core/Bans.php` deleted, now is a Stimulus controller
-   **Deprecated functions**
    -   `src/functions.php`
        -   `myDefine`
        -   `safeescape`
        -   `nltoappon`

### :notebook: NOTES

-   **Important**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
    -   :warning: Avoid, as far as possible, using static classes (e.g. LotgdSetting, Doctrine, LotgdTranslation...) as these classes will be deleted in a future version. Use autowire, dependency injection when possible.
    -   :warning: Version 7.0.0 change templates for use **TailwindCSS**
    -   :warning: Version 8.0.0 deleted old modules system and JaxonPHP
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies
