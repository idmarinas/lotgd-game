# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/master/README.md).   
For historic Changelog [visit](https://github.com/idmarinas/lotgd-game/blob/5.1.0/CHANGELOG.md)

# Version: 6.0.0

### :cyclone: CHANGES

-   **BC** LoTGD Core is a Symfony App (Bundle) since version 6.0.0.
    -   Changed the entire core structure to follow Symfony Framework conventions.
-   :warning: LoTGD is now a Symfony App (Bundle).
-   Moved content of `src/core` to `src/Bundle`
    -   LoTGD follow structure of Symfony App divided in Bundles.
-   **New** login system:
    -   Old password are auto-migrated (but can fail)
        -   Can reset password.
-   **BC** **Entities**
    -   `Lotgd\Core\Entity\Accounts` is now `Lotgd\Core\Entity\User`
        -   Data of `accounts` are migrated to `user`
            -   Not all data are migrated, see diffs for more info.
    -   `Lotgd\Core\Entity\Characters` is now `Lotgd\Core\Entity\Avatar` Characters is a reserved word.

### :star: FEATURES

-   **New** Since 6.0.0 version LoTGD Core is a Symfony Bundle.
    -   All features of Symfony App

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **src/core/Form/ConfigurationType/TrainingType.php** Fixed error with translation keys.

### :x: REMOVES

-   **BC** Remove `src/core/`, `src/local/` and `src/ajax/`
-   **BC** Delete folder `modules/` old module system not work with this version use Bundle system.
    -   Deleted related files/tables in database to modules too.
-   **BC** Delete `AdvertisingBundle` from Core. Now is a independent bundle (can use in other Symfony projects).
    -   If you need/want use in your server can use https://github.com/idmarinas/advertising-bundle
-   **BC** Removed class:
    -   `Lotgd\Core\Application`
    -   `Lotgd\Core\EventManagerAware`
    -   `Lotgd\Core\Hook`
    -   All fixed class use dependency injection:
        -   `Lotgd\Core\Fixed\Doctrine`
        -   `Lotgd\Core\Fixed\FlashMessages`
        -   `Lotgd\Core\Fixed\Format`
        -   `Lotgd\Core\Fixed\HookManager`
        -   `Lotgd\Core\Fixed\Kernel`
        -   `Lotgd\Core\Fixed\Navigation`
        -   `Lotgd\Core\Fixed\Request`
        -   `Lotgd\Core\Fixed\Response`
        -   `Lotgd\Core\Fixed\Sanitize`
        -   `Lotgd\Core\Fixed\Session`
        -   `Lotgd\Core\Fixed\Theme`
        -   `Lotgd\Core\Fixed\Translator`
    -   All pattern class, use dependency injection:
        -   `Lotgd\Core\Pattern\Cache`
        -   `Lotgd\Core\Pattern\Censor`
        -   `Lotgd\Core\Pattern\Container`
        -   `Lotgd\Core\Pattern\Doctrine`
        -   `Lotgd\Core\Pattern\EntityHydrator`
        -   `Lotgd\Core\Pattern\Format`
        -   `Lotgd\Core\Pattern\HookManager`
        -   `Lotgd\Core\Pattern\Http`
        -   `Lotgd\Core\Pattern\Jaxon`
        -   `Lotgd\Core\Pattern\LotgdCore`
        -   `Lotgd\Core\Pattern\Navigation`
        -   `Lotgd\Core\Pattern\Output`
        -   `Lotgd\Core\Pattern\Sanitize`
        -   `Lotgd\Core\Pattern\Settings`
        -   `Lotgd\Core\Pattern\Template`
        -   `Lotgd\Core\Pattern\ThemeList`
        -   `Lotgd\Core\Pattern\Translator`
-   **Twig functions/filters**
    -   Remove function `base_path()`
    -   Remove filter `lotgd_url` not need in Symfony App
    -   Remove functions `var_dump()` and `bdump()`, use `dump()` instead
    -   Remove function `head_title()` this function not have much more usseful. Use block `{% block lotgd_core_head_title %}New Title{% endblock %}` in template.
    -   Remove function `game_version()` this function is not necessary, use Twig Global var `{{ lotgd_core_version }}`
    -   Removed `Helpers` extension:
        -   Function `head_link` removed. 
        -   Function `head_meta` removed. 
        -   Function `head_script` removed. 
        -   Function `head_style` removed. 
        -   Function `inline_script` removed. 
        -   Note: These functions have been replaced by `sonata_block_render_event` and `sonata_block_render`
-   **BC** Global functions:
    -   `is_mail`, no in use in this version, can use Symfony Validator
    -   `_curl`, not in use in this version, can use Symfony Http-Client
    -   `_sock`, not in use in this version, can use Symfony Http-Client
    -   `list_files`, not in use in this version, can use Symfony Finder
    -   `myDefine`, not in use.
    -   `pullurl`, not in use.
    -   `safeescape`, not in use.
    -   `createstring`, not in use.
    -   `urltoarray`, not in use.

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
