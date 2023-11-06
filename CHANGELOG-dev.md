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

# Version: 7.1.0

### :cyclone: CHANGES

-   **BC** Min PHP version needed is `7.4`
-   `assets/lib/components/embed.js` `Lotgd.embed(this)` now also receives the event parameter `Lotgd.embed(this, event)`
-   `Faq menu` moved from Village/Shades menu to Top menu.
    -   Now can see FAQ always.

### :star: FEATURES

-   `stimulus-controller` Petition, add new function for load custom faq. Need pass url as param.
    -   Example of usage
    ```php
        $args[] = [
            'attr' => [
                'data-action' => 'click->petition#loadFaq',
                'data-petitition-url-param' => 'stimulus.php?method=NameOfMethod&controller=NamespaceOfController'
            ],
            'link'    => [
                'section.faq.toc.cities',
                [],
                'cities_module',
            ],
        ];
    ```

### :fire: DEPRECATED

-   `src/functions.php` Mark functions as deprecated:
    -   `myDefine`
    -   `safeescape`
    -   `nltoappon`

### :wrench: FIXES

-   `src/core/Repository/UserRepository.php` Fixed error with place of `Debugger::log()` in function `getUserById`
-   `themes/LotgdModern/templates/page/bio.html.twig` Fixed error with key of translation
-   `translations/en/page_bio+intl-icu.en.yaml` add missin key translation
-   `src/core/Repository/User/Avatar.php` Fixed error when not found news for user, now return a correct empty array
-   `public/bans.php` Fixed error that can add bans
-   Fixed some code smells and vulnerabilities

### :x: REMOVES

-   `public/common_common.php` Deleted code to create file `.env.local.php`
    -   You need to create this file before upgrading from a version earlier than 4.9.0
-   `assets/lib/game/datacache.js` Deleted unused functions
    -   Use console to clear cache.

### :notebook: NOTES

-   **Important**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
    -   :warning: Avoid, as far as possible, using static classes (e.g. LotgdSetting, Doctrine, LotgdTranslation...) as these classes will be deleted in a future version. Use autowire, dependency injection when possible.
    -   :warning: Version 7.0.0 change templates for use **TailwindCSS**
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If you have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies
