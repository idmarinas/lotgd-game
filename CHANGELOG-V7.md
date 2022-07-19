# History of changes for IDMarinas Edition

This is a list of changes made in versions __7.Y.Z__


# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/migration/README.md).  
Visit **_DEV_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-dev.md)  
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V3.md)  
Visit **_V5_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V5.md)  
Visit **_V6_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V6.md)  
Visit **_V8_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V8.md)  

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
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

# Version: 7.1.1

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   `src/core/Tool/Validator.php` Fix error with cheking if email is valid

### :x: REMOVES

-   Nothing

### :notebook: NOTES

-   **Important**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
    -   :warning: Avoid, as far as possible, using static classes (e.g. LotgdSetting, Doctrine, LotgdTranslation...) as these classes will be deleted in a future version. Use autowire, dependency injection when possible.
    -   :warning: Version 7.0.0 change templates for use **TailwindCSS**
    -   :warning: Version 8.0.0 deleted old system of modules and JaxonPHP
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

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
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

# Version: 7.0.4

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Add file `.htaccess` to root folder for redirect to the folder "public/".
    -   You can change `DocumentRoot` in the Apache `VirtualHost` configuration file.

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   Updated for use Tailwind CSS
    -   **src/core/Twig/Extension/Pattern/Commentary.php** Icons now use FontAwesome class names

### :x: REMOVES

-   Nothing

### :notebook: NOTES

-   **Important**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
    -   :warning: Avoid, as far as possible, using static classes (e.g. LotgdSetting, Doctrine, LotgdTranslation...) as these classes will be deleted in a future version. Use autowire, dependency injection when possible.
    -   :warning: Version 7.0.0 change templates for use **TailwindCSS**
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies


# Version: 7.0.3

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **lib/showform.php** Updated for show form with Tailwind CSS
-   Use new name of Stimulus package, this avoid some errors.
    -   **asssets/stimulus/controllers/form/input_range_controller.js**
    -   **asssets/stimulus/controllers/form/submit_controller.js**
-   **systemmail** Remplace `systemmail()` function for service method.
-   **is_email** Remplace `is_email()` function for service method.

### :x: REMOVES

-   Nothing

### :notebook: NOTES

-   **Important**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
    -   :warning: Avoid, as far as possible, using static classes (e.g. LotgdSetting, Doctrine, LotgdTranslation...) as these classes will be deleted in a future version. Use autowire, dependency injection when possible.
    -   :warning: Version 7.0.0 change templates for use **TailwindCSS**
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

# Version: 7.0.2

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **Service** Create a public service `lotgd_http_client` use this instead of `http_client`
-   **Comentary** Fixed error with undefined "Lotgd"

### :x: REMOVES

-   Nothing

### :notebook: NOTES

-   **Important**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
    -   :warning: Avoid, as far as possible, using static classes (e.g. LotgdSetting, Doctrine, LotgdTranslation...) as these classes will be deleted in a future version. Use autowire, dependency injection when possible.
    -   :warning: Version 7.0.0 change templates for use **TailwindCSS**
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies


# Version: 7.0.1

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **Cron commands** Fixed error, now import translator service.
-   **src/core/Repository/MotdRepository.php** Fixed error with type of value returned for method `getLastMotd`
-   **src/functions_old.php** Fix error, now check if function exists
-   **src/core/Character/Stats.php** Make "$val" optional argument too

### :x: REMOVES

-   Nothing

### :notebook: NOTES

-   **Important**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
    -   :warning: Avoid, as far as possible, using static classes (e.g. LotgdSetting, Doctrine, LotgdTranslation...) as these classes will be deleted in a future version. Use autowire, dependency injection when possible.
    -   :warning: Version 7.0.0 change templates for use **TailwindCSS**
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies


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
-   **Form system**
    -   New Form Type Field 
        -   `Lotgd\Core\Form\Type\TextareaLimitType`
            -   This type create a text area with a counter of characters that have limit
        -   `Lotgd\Core\Form\Type\AutocompleteType`
            -   This ty pe create a input field for autocomplete from server.

### :fire: DEPRECATED

-   Deprecated trait: `Lotgd\Core\Controller\Pattern\RenderBlockTrait`
    -   Use `Lotgd\Core\Pattern\LotgdControllerTrait` instead
        -   This trait have other methods used in LoTGD
-   **JaxonPHP** is deprecated and deleted of core in version 8.0.0
    -   You can add this dependencie if your use it in version 8.0.0 and up
    -   Think about migrating all JaxonPHP classes to Stimulus.

### :wrench: FIXES

-   **Twig\Extension\Pattern\PageGen.php** Avoid error _division by cero_

### :x: REMOVES

-   **BC** **Semantic UI/Fomantic UI** is removed and remplace with **TailwindCSS**
-   **BC** **Removed deprecation functions**
    -   **src/functions.php**
        -   `is_email` Use service `LotgdKernel::get("lotgd_core.tool.validator")->isMail($string)` instead
        -   `arraytourl` Use php function `http_build_query` instead.
        -   `urltoarray` Use php function `parse_str` instead.
        -   `createstring` Use php function `serialize` instead.
        -   `list_files` Use php `Symfony Component Finder` instead.
        -   `_curl` Use service `LotgdKernel::get("http_client")` instead.
        -   `_sock` Use service `LotgdKernel::get("http_client")` instead.
        -   `pullurl` Use service `LotgdKernel::get("http_client")` instead.
    -   **BC** Removed files
        -   **lib/holyday_texts.php** `holidayize` Use `LotgdTool::holidayize($text, $type)`
        -   **lib/mountname.php** `getmountname` This function is not used by the core.
        -   **lib/mounts.php** `getmount` Use `LotgdTool::getMount($horse)` instead.
        -   **lib/partner.php** `get_partner` Use `LotgdTool::getPartner($player)` instead.
        -   **lib/pvpwarning.php** `pvpwarning` Use `LotgdKernel::get("Lotgd\Core\Pvp\Warning")->warning($dokill)` instead.
        -   **lib/pvpsupport.php** 
            -   `setup_pvp_target` Use `LotgdKernel::get("Lotgd\Core\Pvp\Support")->setupPvpTarget($characterId)` instead.
            -   `pvpvictory` Use `LotgdKernel::get("Lotgd\Core\Pvp\Support")->pvpVictory($badguy, $killedloc)` instead.
            -   `pvpdefeat` Use `LotgdKernel::get("Lotgd\Core\Pvp\Support")->pvpDefeat($badguy, $killedloc)` instead.
        -   **lib/names**
            -   `get_player_title` Use `LotgdTool::getPlayerTitle($old)` instead.
            -   `get_player_basename` Use `LotgdTool::getPlayerBasename($old)` instead.
            -   `change_player_name` Use `LotgdTool::changePlayerName($newname, $old)` instead.
            -   `change_player_ctitle` Use `LotgdTool::changePlayerCtitle($nctitle, $old)` instead.
            -   `change_player_title` Use `LotgdTool::changePlayerTitle($ntitle, $old)` instead.
        -   **lib/pageparts.php** 
            -   `wipe_charstats` Use `LotgdKernel::get("Lotgd\Core\Character\Stats")->wipeStats()` instead.
            -   `addcharstat` Use `LotgdKernel::get("Lotgd\Core\Character\Stats")->addcharstat($label, $value)` instead.
            -   `getcharstat` Use `LotgdKernel::get("Lotgd\Core\Character\Stats")->getcharstat($cat, $label)` instead.
            -   `setcharstat` Use `LotgdKernel::get("Lotgd\Core\Character\Stats")->setcharstat($cat, $label, $val)` instead.
            -   `getcharstat_value` Use `LotgdKernel::get("Lotgd\Core\Character\Stats")->getcharstat($cat, $label)` instead.
            -   `getcharstats` Use `LotgdKernel::get("Lotgd\Core\Service\PageParts")->getCharStats($buffs)` instead.
            -   `charstats` Use `LotgdKernel::get("Lotgd\Core\Service\PageParts")->charStats($return)` instead.
        -   **lib/personal_functions.php** `killplayer` Use `LotgdKernel::get('lotgd_core.tool.staff')->killPlayer($explossproportion, $goldlossproportion)` instead.
        -   **lib/systemmail.php** `systemmail` Use `LotgdKernel::get('lotgd_core.tool.system_mail')->send($to, $subject, $body, $from, $noemail)` instead.
        -   **lib/titles.php** 
            -   `valid_dk_title` Use `LotgdTool::validDkTitle($title, $dks, $gender)` instead.
            -   `get_dk_title` Use `LotgdTool::getDkTitle($dks, $gender, $ref)` instead.
        -   **src/core/Application.php** `Lotgd\Core\Application` Use `Lotgd\Core\Kernel` instead.
-   **BC** Removed some traits 
    -   `src/core/Pattern/Cache.php` 
    -   `src/core/Pattern/Censor.php` 
    -   `src/core/Pattern/Container.php` 
    -   `src/core/Pattern/Doctrine.php` 
    -   `src/core/Pattern/EntityHydrator.php` 
    -   `src/core/Pattern/Format.php` 
    -   `src/core/Pattern/Http.php` 
    -   `src/core/Pattern/Jaxon.php` 
    -   `src/core/Pattern/LotgdCore.php` 
    -   `src/core/Pattern/Navigation.php` 
    -   `src/core/Pattern/Output.php` 
    -   `src/core/Pattern/Sanitize.php` 
    -   `src/core/Pattern/Settings.php` 
    -   `src/core/Pattern/Template.php` 
    -   `src/core/Pattern/ThemeList.php` 
    -   `src/core/Pattern/Translator.php` 
    -   _Note_: use Dependency Injection.


### :notebook: NOTES

-   **Important**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
    -   :warning: Avoid, as far as possible, using static classes (e.g. LotgdSetting, Doctrine, LotgdTranslation...) as these classes will be deleted in a future version. Use autowire, dependency injection when possible.
    -   :warning: Version 7.0.0 change templates for use **TailwindCSS**
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies
