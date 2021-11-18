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

# Version: 7.0.0

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   **TailwindCSS** new Framework for style APP.
    -   **For create new themes can use `tailwind.config.js`
-   **Twig Template System**
    -   New functions:
        -   `stimulus_url` Get a valid stimulus url like `"stimulus.php?method=index&controller=Controller`
            -   Usage: `{{ stimulus_url('Controller\Name', 'method_name', '&query=value') }}`
-   **Notifications System**
    -   Add a new notifications system (Toast notifications)
    -   Use Stimulus to show notifications
    -   For add a notification you can use Trait `Lotgd\Core\Pattern\LotgdControllerTrait` in your controller.
        -   Use method `$this->addNotification('type', 'Notification message');` this is the basic format.
        -   This is the alternative format.
            ```php
                $this->addNotification('type', [
                    'title' => 'Notification Title', 
                    'notification' => 'Notification message', 
                    'close' => false,
                    'duration' => 7000 // Time in miliseconds
                    'id' => 'id-for-notification' //-- By default is auto-generated
                ]);
            ```

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   Nothing

### :x: REMOVES

-   **Semantic UI/Fomantic UI** is removed and remplace with **TailwindCSS**

### :notebook: NOTES

-   **Important**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
    -   :warning: Avoid, as far as possible, using static classes (e.g. LotgdSetting, Doctrine, LotgdTranslation...) as these classes will be deleted in a future version. Use autowire, dependency injection when possible.
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies
