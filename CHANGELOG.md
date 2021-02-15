# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/master/README.md).   
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V3.md)  
Visit **_V4_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V4.md)  
Visit **_V5_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V5.md)  

# Version: 6.0.0

### :cyclone: CHANGES

-   :warning: LoTGD is now a Symfony App.

### :star: FEATURES

-   **New** Since 6.0.0 version LoTGD Core is a Symfony App.

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **lib/modules/objectpref.php** Fixed error: now use same cache service. So not give problems with get/set object prefs
-   **src/core/Form/ConfigurationType/TrainingType.php** Fixed error with translation keys.
-   **src/core/Http/Response.php**  Fixed errors:
    -   `pageTitle()` Now replace title correctly.
    -   `pageDebug()` Param $text can be mixed
-   **lib/figthnav.php** Fixed, now show name of creature when is target.

### :x: REMOVES/Break Changes

-   **BC** So many to write here, ^_^, in version 6.0.0 LoTGD Core is a Symfony App.

### :notebook: NOTES

-   **Notes**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies
