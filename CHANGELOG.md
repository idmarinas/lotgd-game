# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/master/README.md).   
For historic Changelog [visit](https://github.com/idmarinas/lotgd-game/blob/5.1.0/CHANGELOG.md)

# Version: 6.0.0

### :cyclone: CHANGES

-   :warning: LoTGD is now a Symfony App.
-   Moved content of `src/core` to `src`
    -   LoTGD follow structure of Symfony App

### :star: FEATURES

-   **New** Since 6.0.0 version LoTGD Core is a Symfony App.

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **src/core/Form/ConfigurationType/TrainingType.php** Fixed error with translation keys.

### :x: REMOVES/Break Changes

-   **BC** So many to write here, ^_^, in version 6.0.0 LoTGD Core is a Symfony App.
-   **BC** Delete folder `modules/` old module system not work with this version use Bundle system.
-   **BC** Delete `AdvertisingBundle` from Core. Now is a independent bundle.
    -   If you need/want your your server can use https://github.com/idmarinas/advertising-bundle:
        ```bash
        composer require idmarinas/advertising-bundle
        ```
-   **BC** Removed class:
    -   `Lotgd\Core\Application`
    -   `Lotgd\Core\EventManagerAware`
    -   `Lotgd\Core\Hook`
    -   All fixed class:
        - `Lotgd\Core\Fixed\Doctrine`
        - `Lotgd\Core\Fixed\FlashMessages`
        - `Lotgd\Core\Fixed\Format`
        - `Lotgd\Core\Fixed\HookManager`
        - `Lotgd\Core\Fixed\Kernel`
        - `Lotgd\Core\Fixed\Navigation`
        - `Lotgd\Core\Fixed\Request`
        - `Lotgd\Core\Fixed\Response`
        - `Lotgd\Core\Fixed\Sanitize`
        - `Lotgd\Core\Fixed\Session`
        - `Lotgd\Core\Fixed\Theme`
        - `Lotgd\Core\Fixed\Translator`
-   **BC** _Entities_
    -   `src/Entity/Characters.php` Rename to `src/Entity/Avatar.php`. Characters is a reserved word.
-   **Twig functions/filters**
    -   Remove function `base_path()`
    -   Remove filter `lotgd_url` not need in Symfony App
    -   Remove functions `var_dump()` and `bdump()`, use `dump()` instead

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
