# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/migration/README.md).  
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V3.md)  
Visit **_V4_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V4.md)  
Visit **_V5_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V5.md)  
Visit **_V6_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V6.md)  

# Version: 6.1.0

### :cyclone: CHANGES

-   **Changes in some files in `public/` folder**
    -   I reduce code in this pages to this code:
        ```php
        require_once 'common.php';

        //-- Init page
        \LotgdResponse::pageStart();

        //-- Call controller
        \LotgdResponse::callController('CONTROLLER_NAME');

        //-- Finalize page
        \LotgdResponse::pageEnd();
        ```
    -   _Note_: with this I'm preparing the Core to migrate it to a Symfony App (Routing)

### :star: FEATURES

-   **Commentary system** 
    -   A translatable comment can now be added so that when this comment is displayed it is translated into the current language of the game.
        -   To add this comment only need add `translation_domain` to comment data array
        -   Example: 
            ```php
            $commentaryService->saveComment([
                'section' => 'any_section',
                'comment' => 'translation.key', //-- Option 1
                // 'comment' => '/me translation.key', //-- Option 2, add a comment with command (any available command can be used)
                //-- Note: No use /grem command, since this command is special for deleting the last comment written by the user.
                'translation_domain' => 'my_domain'
            ]);
            ```
        -   _Note_: With this change the commentary is affected by the language of the game at all times and not only when the commentary is added.
    -   **New command** for comments `/grem` or `:grem` or `::grem` in chat comment and a small horde of Gremlin will delete the last comment you have written as long as it has not been more than 3 minutes.

### :fire: DEPRECATED

-   **lib/systemmail.php** 
    -   `systemmail` is deprecated. Use `LotgdKernel::get('lotgd_core.tool.system_mail')->send($to, $subject, $body, $from, $noemail)` instead.
-   **src/core/functions.php** 
    -   `is_email` is deprecated. Use service `LotgdKernel::get('lotgd_core.tool.validator')->isMail(string)` instead.
    -   `arraytourl` is deprecated. Use php function `http_build_query` instead.
    -   `urltoarray` is deprecated. Use php function `parse_str` instead.
    -   `createstring` is deprecated. Use php function `serialize` instead.
    -   `list_files` is deprecated. Use component `Symfony Finder` instead.
    -   `_curl`, `_sock` and `pullurl` is deprecated. Use service `LotgdKernel::get('http_client')` instead
-   **lib/dump_item.php**
    -   `dump_item` and `dump_item_ascode` is deprecated and deleted un future version.

### :wrench: FIXES

-   Nothing

### :x: REMOVES

-   Nothing

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
