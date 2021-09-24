# History of changes for IDMarinas Edition

This is a list of changes made in versions __3.*.*__


## Changes made for IDMarinas Edition

See CHANGELOG.txt for see changes made for Oliver Brendel +nb Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details. 
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/migration/README.md).  
Visit **_DEV_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-dev.md)  
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V2.md)  
Visit **_V4_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V4.md)  
Visit **_V5_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V5.md)  
Visit **_V6_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V6.md)  
Visit **_V7_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V7.md)  


# Version: 3.0.0

### :cyclone: CHANGES

-   **Improve** performance and prevent security vulnerabilities for external links in this files:
    -   **common.php**
    -   **lotgdnet.php**
    -   **source.php**
    -   **lib/nav.php**
    -   **lib/about/about_default.php**
    -   **lib/about/about_license.php**
    -   **lib/configuration/configuration_cronjob.php**
    -   **lib/installer/installer_sqlstatements.php**
    -   **lib/installer/installer_stage_1.php**
    -   **lib/installer/installer_stage_3.php**
    -   **templates/paypal.twig**
-   **translatetool.php** It uses the new way of generating queries to the DB (this method avoids problems with some characters when making queries, like for example the simple quotation)
-   **common.php** Now use Service Manager to load some factories. This method allows to use any factory and always load the same.
    -   Factories can be remplaced for your own
    -   `ob_start()` not use anymore `ob_gzhandler`
-   **lib/pageparts.php** Updating the JavaScript function name to the new name (Jaxon)
    -   Stat "Spirits" and "Gold" always show (not only when alive)
    -   Spirits "DEAD" only translate when show
-   _Using Service Manager_ in this files

    -   **lib/nav.php** Blockednavs are in Service Manager
    -   **lib/http.php**
    -   **lib/datacache.php**
    -   **lib/output.php** For generate class that use the output functions
    -   **lib/redirect.php** For load Output Collector

-   **lib/e_rand.php** Some improvements and added comments to functions
-   **lib/creaturefunctions.php** Now param `$packofmonsters` have a default value
-   **lib/modules.php** This file has been divided into different files, to improve comprehension.
    -   New files:
        -   **lib/modules.php**
        -   **lib/modules/actions.php**
        -   **lib/modules/blockunblock.php**
        -   **lib/modules/event.php**
        -   **lib/modules/hook.php**
        -   **lib/modules/injectmodule.php**
        -   **lib/modules/modulestatus.php**
        -   **lib/modules/objpref.php**
            -   Now used forced cache to get ObjPref for a given module. This data is a object config and not change so much.
        -   **lib/modules/prefs.php**
            -   In this part not used cache because with some modules not work good.
        -   **lib/modules/settings.php**
-   :warning: **_IMPORTANT_**
    -   **Jaxon** Files for jaxon has moved to new dir
    -   Can add your own files for jaxon in `src/ajax/local`
    -   **lib/class/template.php** Is now a class of static functions no needed be instantiated `LotgdTheme::`
        -   Using class `LotgdTheme::` for render templates:
        -   **armor.php**
        -   **clan.php**
        -   **create.php**
        -   **home.php**
        -   **weapons.php**
        -   **lib/lotgd_mail.php**
        -   **lib/nav.php**
        -   **lib/pageparts.php**
        -   **lib/about/about_listmodules.php**
        -   **lib/battle/functions.php**
        -   **lib/configuration/configuration_cache.php**
    -   **lib/class/lotgdFormat.php** Is now a class of static functions no needed be instantiated
        -   Using class `LotgdFormat::` for format numbers and any dates in:
            -   **bank.php**
            -   **donators.php**
            -   **list.php**
            -   **stats.php**
            -   **lib/commentary.php**
            -   **lib/template.class.php**
            -   **lib/bans/case\_.php**
            -   **lib/bans/case_removeban.php**
            -   **lib/bans/case_searchban.php**
            -   **lib/user/user\_.php.php**
            -   **lib/user/user_removeban.php.php**
            -   **lib/user/user_searchban.php.php**
-   **lib/installer/installer_stage_6.php** File `dbconnect.php` are in a new folder and have a new structure.
-   **THEME**
    -   Updated Semantic UI version 2.4.0 => 2.4.2

### :star: FEATURES

-   Now Lotgd IDMarinas Edition supported prefix for tables. You can add a prefix to name of tables in database. But may be not are full supported for any query in game. Remember use function `DB::prefix(string)` for add a prefix to name of table.
-   **lib/pageparts.php** Transfer character stats to a factory
-   **lib/class/doctrine.php** Add a Doctrine ORM to core of Lotgd. Now you can use in your modules.
    -   This, is perhaps, the prelude to its use in the whole core. :laughing:
    -   Functions allowed for now:
        -   `Doctrine::getRepository(string [EntityClassName])`
        -   `Doctrine::syncEntity(string [EntityClassName])` Synchronize a Entity with database.
        -   `Doctrine::syncEntities(array [EntityClassName])` Synchronizes an array of Entities with database.

### :fire: DEPRECATED

-   **lib/class/dbwrapper.php** Function `query_cached` is deprecated and deleted in a future version
    -   Use data cache system to cache data of query when needed
-   **lib/pageparts.php** Function `popup()` is deprecated and deleted in 3.1.0 version

### :wrench: FIXES

-   **bank.php** Fixed error by which you could not borrow money
-   **rumodule.php** Fixed error with link added with addnav
-   **lib/buffs.php** Fixed error with undefined index

### :x: REMOVES

-   **common.php** Code removed for version upgrade previous versions 1.0.0 IDMarinas edition and below
    -   This makes that from the 3.0.0 version it is impossible to update a previous version to the 1.0.0 IDmarinas Edition
-   **settings.php** Removed unused file
-   **lib/phpmailer/** Deleted all files, not used in Lotgd Core. If you need, you can load via Composer
-   **lib/sendmail.php** Removed from the core of the game, was not being used.
-   _Removed deprecate functions_
    -   **lib/datetime.php**
    -   **lib/class/dbwrapper.php**
    -   **lib/http.php**
    -   **lib/forestoutcomes.php**
    -   **lib/showform.php**
    -   **lib/template.class.php**
    -   **lib/template.php**

### :notebook: NOTES

-   **Optimization** Many files `.php`' have had a slight optimization of code using CS Fixer.

