# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/master/README.md).   
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V3.md)  
Visit **_V4_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V4.md)  

# Version: 5.1.0

### :cyclone: CHANGES

-   **Moved** `templates/lotgd/` to `themes/LotgdTheme/templates/` folder
-   **Moved** `templates_core/` to `themes/LotgdTheme/templates/admin/` folder

### :star: FEATURES

-   **New Theme System**
    -   This version included new Theme System powered by [SyliusThemeBundle](https://github.com/Sylius/SyliusThemeBundle)
    -   This new system allow to customize appearance of LoTGD more easy. This
    -   And them can change the theme of LoTGD easy.
        -   At the moment it does not allow the user to change the theme.
    -   Theme structure in `themes/` folder. 
        ```
        AcmeTheme
        ├── theme.json
        ├── public
        │   └── asset.jpg
        ├── templates
        │   ├── bundles
        │   │   └── AcmeBundle
        │   │       └── bundleTemplate.html.twig
        |   └── template.html.twig
        └── translations
           └── messages.en.yml
        ```
    -   Olso include [Sonata Blocks](https://github.com/sonata-project/SonataBlockBundle)
        -   Note: `sonata_block_render_event()` not working.

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   Nothing

### :x: REMOVES/Break Changes

-   Removed `src/core/Template/Template.php` 
-   Removed `src/core/Twig/Loader/LotgdFilesystemLoader.php`

### :notebook: NOTES

-   **Notes**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
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

# Version: 5.0.0

### :cyclone: CHANGES

-   Static class **LotgdTranslator** now get a instance of `Symfony Translator`
-   `Lotgd\Core\Http\Request` and `Lotgd\Core\Http\Response`
    -   Move function `setCookie()` from `Request` to `Response`
-   Moved `lib/constants.php` to `src/constants.php`

### :star: FEATURES

-   **New installation system**
    -   Use Symfony Console and commands for run a install and upgrade the game core.
        -   Game only detect if are installed, not detect if need a upgrade.
    -   Install game is easy run the follow command: `php bin/console lotgd:install`
    -   Note: As a Symfony App (50% or so ^_^) you need run commands in terminal from root directory of game. 
    -   _New installer_ not install modules, you need manual install the bundle.
        -   Remember that **Old module system** are deprecated and deleted in version 7.0.0

### :fire: DEPRECATED

-   All static class in **src/core/Fixed/** folder are deprecated. If possible use the services as you would do in a Symfony app.
    -   All these classes will be removed in version 7.0.0.
-   All pattern class in **src/core/Pattern/** folder are deprecated. If possible use Dependency Injection.
    -   All these patterns will be removed in version 7.0.0.
-   **Old module system** are deprecated and deleted in version 7.0.0
    -   Use a Symfony Bundle type module when you can.
        -   You can see a little example of Bundle in `lib/AdvertisingBundle` and you can search for the web.

### :wrench: FIXES

-   **lib/deathmessage.php** and **lib/taunt.php** fixed error with translation function (now pass empty array as param)
-   **lib/battle/functions.php** fixed error with name of index of Overlord
-   **public/graveyard.php** fixed error in format of arrays
-   **templates/lotgd/pages/_macros/_battle.html.twig** fixed error with text domain

### :x: REMOVES/Break Changes

-   **BC** remove LoTGD console `bin/lotgd` use `bin/console`
    -   Removed command `src/core/Command/StorageCacheClearCommand.php` 
    -   Removed command `src/core/Command/StorageCacheStatsCommand.php`
        -   Symfony have the same command. `php bin/console cache:clear` and `php bin/console about` can see stats.
            -   This command not touch `storage/` folder only `var/` folder.
-   **BC** removed files/config related to **Laminas Form** use **Symfony Form** instead.
-   **BC** removed Service Manager to create factories **Laminas Service Manager** use LoTGD Kernel instead (and all related files).
-   **BC** removed static class **LotgdCache** because **Laminas Cache** and **Symfony Cache** work diferent.
-   **BC** removed static class **Dbwrapper** as **DB::** because **Laminas DB** is deleted use **Doctrine**
-   **BC** removed static class **LotgdLocator** because **Laminas Service Manager** is deleted use **LotgdKernel** for get services.
-   **BC** removed function of `get/set(container)` in file `src/core/Pattern/Container.php`
-   **BC** removed function of pattern `src/core/Pattern/Cache.php`
    -   `getCache()` use `getCacheApp()` or `getCacheAppTag` for a tagged version.
        -   Not create an alias because _Laminas_ and _Symfony_ work diferent.
-   **BC** **Twig Extensions** 
    -   **Translation extension**
        -   Removed filters:
            -   `t` use `trans` filter
            -   `ts` use `trans` filter
            -   `tl` use `trans` filter
            -   `tst` use `trans` filter
            -   `tmf` use  `mf` (this funcion not translate only is a formatter)
        -   Removed token `{% translate_default_domain 'foobar' %}` use `{% trans_default_domain 'foobar'%}`
    -   **Core Extension**
        -   Removed function `{{ page_title() }}` use `{{ head_title() }}` instead.
        -   Removed function `{{ include_layout() }}`
        -   Removed function `{{ include_theme() }}`
        -   Removed function `{{ include_module() }}`
-   Removed cronjob `cronjob/cacheoptimize.php`
-   Removed deprecated function of `lib/datetime.php` file:
    -   `reltime()` use `LotgdFormat::relativedate($indate, $default)` instead
-   **BC** remove file `lib/translator.php` and all functions:
    -   `translator_setup()`
    -   `translate()`
    -   `sprintf_translate()`
    -   `translate_inline()`
    -   `translate_mail()`
    -   `tl()`
    -   `translate_loadnamespace()`
    -   `tlbutton_push()`
    -   `tlbutton_pop()`
    -   `tlbutton_clear()`
    -   `enable_translation()`
    -   `tlschema()`
    -   `translator_check_collect_texts()`
    -   `translator_uri()`
    -   `translator_page()`
    -   `comscroll_sanitize()`
-   Removed Jaxon command `src/ajax/core/Cache.php` 
-   Removed entity `src/core/Entity/Nastywords.php` not in use
-   **BC** :warning: Tables deleted:
    -   `translations` and `untranslated` not in use, use new translation system
    -   `nastywords` not in use, use new Censor system `Lotgd\Core\Output\Censor` can get as service
-   **BC** remove validators not in use.
    -   **src/core/Validator/Db/NoObjectExists.php** 
    -   **src/core/Validator/Db/ObjectExists.php**
    -   **src/core/Validator/DelimeterIsCountable.php**

### :notebook: NOTES

-   **Notes**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
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
