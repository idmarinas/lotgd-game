# History of changes for IDMarinas Edition

This is a list of changes made in versions __6.*.*__


# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/migration/README.md).  
Visit **_DEV_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-dev.md)  
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V3.md)  
Visit **_V5_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V5.md)  
Visit **_V7_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V7.md)  

# Version: 6.2.0

### :cyclone: CHANGES

-   **New CronJob system**
    -   LoTGD IDMarinas Edition user `Cron Bundle` to execute crons of core.
    -   Usage of `public/cronjobs.php` is Deprecated.
        -   For now not delete this file and crons register in DataBase.
    -   To create a cron job on the new system, execute the command `php bin/console cron:create` and follow the steps below
    -   Note: this new system commands to be executed are console commands, so they have to be registered and can be used with the `php bin/console command_to_use` command.
    -   If you want use this feature, make sure you setup a cronjob on your machine confix/plesk/cpanel or any other admin panel. 
        -   This is de unique cronjob need create copy:
            -   `* * * * * /path/to/project/bin/console cron:run 1>> /dev/null 2>&1`
                -   Change **_"/path/to/project"_** to where is the game installed.
                -   This cronjob execute all CronJobs in the game, but only CronJobs registered with new CronJob System.
    -   **IMPORTANT**: 
        -   _New day cron_ in new cronjob system not execute hook of modules. If you need execute this old hook use old cronjob system for new day.
            -   _New day cron_ in new cronjob system are disabled by default.
        -   Remember deleted duplicated commands. If you want use both systems.
    -   _Note_: The complete migration from the OLD Cronjob system to the NEW one will be done in version 8.0.0. 

### :star: FEATURES

-   **Stimulus "route"**
    -   Added file `public/stimlus.php` You can use Stimulus for load a small blocks for HTML code.
    -   Only need call to this route `stimulus.php?method=METHOD_NAME&controller=urlencode(CONTROLLER_NAME)`
    -   Example:
        ```html
            <!-- In eny template -->
            <div data-controller="content-loader" data-content-loader-url-value="stimulus.php?method=index&controller=<?php echo urlencode(ContentController::class) ?>"></div>
        ```

        ```php
            // src/local/Controller/ContentController.php
            use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

            class ContentController extends AbstractController
            {
                public function index()
                {
                    return $this->render('some_template.html.twig', []);
                }
            }
        ```

        ```js
            // src/controllers/content_loader_controller.js
            import { Controller } from "stimulus"

            export default class extends Controller 
            {
                static values = { url: String }

                connect() 
                {
                    this.load()
                }

                load() 
                {
                    fetch(this.urlValue)
                    .then(response => response.text())
                    .then(html => this.element.innerHTML = html)
                }
            }
        ```

### :fire: DEPRECATED

-   **public/cronjobs.php** and **cronjob/** is Deprecated, use new Cron Bundle to execute and create your own crons.
    -   This method of creating and running cron jobs will be removed in version 8.0.0
-   **lib/serverfunctions.class.php** Is deprecated now is a service: 
    -   `isTheServerFull` use `LotgdKernel::get("lotgd_core.service.server_functions")->isTheServerFull()` instead or dependency injection.
    -   `resetAllDragonkillPoints` use `LotgdKernel::get("lotgd_core.service.server_functions")->resetAllDragonkillPoints($acctid)` instead or dependency injection
-   **lib/pageparts.php** All functions:
    -   `wipe_charstats` use `LotgdKernel::get("Lotgd\Core\Character\Stats")->wipeStats()` instead.
    -   `addcharstat` use `LotgdKernel::get("Lotgd\Core\Character\Stats")->addcharstat($label, $value)` instead.
    -   `getcharstat` use `LotgdKernel::get("Lotgd\Core\Character\Stats")->getcharstat($cat, $label)` instead.
    -   `setcharstat` use `LotgdKernel::get("Lotgd\Core\Character\Stats")->setcharstat($cat, $label, $val)` instead.
    -   `getcharstat_value` use `LotgdKernel::get("Lotgd\Core\Character\Stats")->getcharstat($cat, $label)` instead.
    -   `getcharstats` use `LotgdKernel::get("Lotgd\Core\Service\PageParts")->getCharStats($buffs)` instead.
    -   `charstats` use `LotgdKernel::get("Lotgd\Core\Service\PageParts")->charStats($return)` instead.

### :wrench: FIXES

-   **src/core/Service/ServerFunction.php** Fix error when reset dragon kills

### :x: REMOVES

-   **lib/graveyard/** folder and content:
    -   `lib/graveyard/case_battle_search.php` and `lib/graveyard/case_question.php` code are moved to controller.  

### :notebook: NOTES

-   Apply Rector rules to files in `src/` and `public/`
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

# Version: 6.1.1 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **Include fixes of version 5.5.9**
-   **src/core/Combat/Battle/Buff.php** 
    -   Fix error, method `->addContextToRoundEnemy()` need only 1 param
    -   Fix error with method `->substitute()` now pass correct params
-   **src/core/Combat/Battle/Movement.php** Fix error, method `->addContextToRoundEnemy()` need only 1 param
-   **src/core/Combat/Battle/Process.php** Fix error, method `->addContextToRoundEnemy()` need only 1 param
-   **public/shades.php** Fix error, now use correct controller
-   **src/core/Controller/NewDayController.php** and **src/core/Controller/NewDayController/DragonPointSpendTrait.php**: Fix error, now invoke spend points when have more than 1 point to spend.
-   **themes/LotgdTheme/templates/page/_blocks/_newday.html.twig**
    -   Add class format to inputs.
    -   Fix error, now show correct translate text
    -   Fix error with colspan
    -   Now show points left
    -   Apply min and max values in input

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

# Version: 6.1.0

### :cyclone: CHANGES

-   **Changes in some files in `public/` folder**
    -   I have reduced the code of these pages to this code:
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
-   **Repository**: Now all class extends `Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository` and use dependency injection when is necesary.

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
-   **Occurrence system**
    -   The old and the new event system will work at the same time during the transition.
        -   Activation order
            -   First: activate Occurrence system.
            -   Second: activate the old event system.
                -   Only if Occurrence system is not active.
    -   This system replace old **Special events** in game in version 8.0.0
    -   Zones with this new system:
        - `forest`
        - `gardens`
        - `graveyard`
        - `inn`
        - `village`
            -   All these zones pass four parameters to an event object.
            -   Example for `forest` zone:
            ```php
                $event = \LotgdKernel::get('occurrence_dispatcher')->dispatch('forest', null, [
                    'translation_domain'            => $textDomain,
                    'translation_domain_navigation' => $textDomainNavigation,
                    'route'                         => 'forest.php',
                    'navigation_method'             => 'forestNav',
                ]);
            ```
    -   Can add more occurrences to game only add your occurrences to file:
        ```yaml
            # config/packages/lotgd_occurrence.yaml
            lotgd_occurrence: # Required
                # Prototype
                name: # Name for your occurrance
                    # Probability of activate this event zone. Int 0-10000 (10000 is equal to 100.00%)
                    probability: ~ # Required
                    # Optional: Maximun number of events that can be activated in this zone.
                    max_activation: 2
        ```
    -   Work similar to Symfony Event Dispatcher
        -   Create a subscriber that implements `Lotgd\CoreBundle\OccurrenceBundle\OccurrenceSubscriberInterface` 
            -   Example:
            ```php
                use Lotgd\CoreBundle\OccurrenceBundle\OccurrenceSubscriberInterface;
                use Symfony\Component\EventDispatcher\GenericEvent;

                class ExampleSubscriber implements OccurrenceSubscriberInterface
                {
                    public function onMessage(GenericEvent $event)
                    {
                        //-- Do something
                    }

                    public static function getSubscribedOccurrences()
                    {
                        return [
                            'forest' => ['onMessage', 10000, OccurrenceSubscriberInterface::PRIORITY_INFO]
                        ];
                    }
                }
            ```
    -   _Note_: [Fairy event](https://github.com/lotgd-core/fairy-bundle) is an first example of usage of this new feature.

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

# Version: 6.0.4 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **Include fixes of version 5.5.8**

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

# Version: 6.0.3 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **Include fixes of version 5.5.7**

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

# Version: 6.0.2 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **Include fixes of version 5.5.6**

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

# Version: 6.0.1

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **Include fixes of version 5.5.5**

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

# Version: 6.0.0

### :cyclone: CHANGES

-   **BC** **Change entity Accounts to User**
    -   Rename Entity `Lotgd\Core\Entity\Accounts` to `Lotgd\Core\Entity\User`. This is for preparing to Symfony App.
        -   Removed some fields:
            -   `translatorlanguages` It has no use in this version and in the latest versions either.
            -   `beta` Feature of beta not is funcional, so no need this field. 
        -   Renamed some fields:
            -   `character` is now `avatar`. character and characters is a reserved word.
-   **BC** **Change entity Characters to Avatar**
    -   Rename Entity `Lotgd\Core\Entity\Characters` to `Lotgd\Core\Entity\Avatar`. character and characters is a reserved word.
-   **Login/passwords** Change method to login and hashed password
    -   Using aproach of Symfony hash password and use migrating for not break old passwords.
    -   All new accounts use new password hash, and old accounts migrated to new hash when login.
-   **Character Backup**
    -   Add option to encrypt data when save to file.
        -   `encrypt` key same structure of entities
            ```php
            [
                // 'Entity:Name' => encrypt: true|false
                'LotgdLocal:SensitiveEntity' => true,
                'Lotgdcore:SensitiveEntity' => true
            ]
            ```
            -   Use same name in entities and encrypt
    -   **Updated character restore** for new Avatar and User entities
        -   **Installer of version 6.0.0** Update old backups to new Avatar and User entity
-   **Combat as service**
    -   Deleted `public/battle.php` not use more in your modules/bundles not work as expected.
    -   Method `fightNav` is part of Battle service
    -   When calculate buffs now use Symfony Expression Language
        -   Modules that use `<module|variable>` replacements for `get_module_pref('variable','module')`
            -   Use `get_module_pref` normaly
        -   Modules that use `<variable>` replacements for `$session['user']['variable']`
            -   Use `character_attr('variable')`
    -   Example:
        ```php
        /** @var \Lotgd\Core\Combat\Battle */
        $serviceBattle = \LotgdKernel::get('lotgd_core.combat.battle');

        //-- Battle zone.
        $serviceBattle
            ->initialize() //--* Initialize the battle
            //-- Configuration
            ->setBattleZone('mine') //-- Battle zone, by default is "forest".
            
            ->battleStart() //--* Start the battle.
            ->battleProcess() //--* Proccess the battle rounds.
            ->battleEnd() //--* End the battle for this petition
            ->battleResults() //--* Add results to response by default (use ->battleResults(true) if you want result results)
        ;

        if ($serviceBattle->isVictory())
        {
            //-- Do anything when victory
        }
        elseif ($serviceBattle->isDefeat())
        {
            //-- Do anything when defeated
        }
        //-- Only show figth navs when not have winner
        elseif ( ! $serviceBattle->battleHasWinner())
        {
            $serviceBattle->fightNav();
        }
        ```
    -   Updated files with new Battle service:
        -   `public/forest.php` 
        -   `public/dragon.php` 
        -   `public/graveyard.php` 
        -   `public/train.php` 
        -   `public/pvp.php` 


### :star: FEATURES

-   **Module/Bundle migrating**
    -   Now `runmodule.php` script work with controllers too. So you can migrate your module to bundle more easy.
        -   Usage:
            -   Create controllers for your new Bundle
            -   If your module use: 
                ```php
                    function MODULE_getmoduleinfo()
                    {
                        return [
                            //....
                            'allowanonymous'      => true,
                            'override_forced_nav' => true,
                            //....
                        ];
                    }
                ```
                -   Replace this with:
                    -   Implement in controller interface `Lotgd\Core\Controller\LotgdControllerInterface` 
                        -   Configure as you need. Remember that this configuration is for ALL methods in controller.
                            -   If  you need some method public and other for user logged, create diferent controllers.
            -   Replace `runmodule.php?module=MODULE_NAME` for `runmodule.php?method=METHOD_NAME&controller=urlencode(CONTROLLER_NAME)`
                ```php 
                    //-- Example
                    $url = 'runmodule.php?method=index&controller='.urlencode(\Lotgd\Local\Controller\ModuleController::class);
                    \LotgdNavigation::addNav('Do anything', $url);
                ```
-   **New console command** Logout all users with inactive sessions `lotgd:user:logout` 

### :fire: DEPRECATED

-   **lib/pvpsupport.php** All functions:
    -   `setup_pvp_target` use `LotgdKernel::get("Lotgd\Core\Pvp\Support")->setupPvpTarget($characterId)` instead
    -   `pvpvictory` use `LotgdKernel::get("Lotgd\Core\Pvp\Support")->pvpVictory($badguy, $killedloc)` instead
    -   `pvpdefeat` use `LotgdKernel::get("Lotgd\Core\Pvp\Support")->pvpDefeat($badguy, $killedloc)` instead
-   **lib/pvpwarning.php** Function `pvpwarning` use `LotgdKernel::get('Lotgd\Core\Pvp\Warning')->warning($dokill)`
-   **lib/mountname.php** Function `getmountname` This function is not used by the core. And not need for know name of mount.
-   **lib/mounts.php** Function `getmount` use `LotgdTool::getMount(int $horse)` instead.
-   **lib/partner.php** Function `get_partner` use `LotgdTool::getPartner(bool $player)` instead.
-   **lib/title.php** All functions:
    -   `valid_dk_title` use `LotgdTool::validDkTitle($title, $dks, $gender)` instead.
    -   `get_dk_title` use `LotgdTool::getDkTitle($dks, $gender, $ref)` instead.
-   **lib/names.php** All functions:
    -   `get_player_title` use `LotgdTool::getPlayerTitle($old)` instead.
    -   `get_player_basename` use `LotgdTool::getPlayerBasename($old)` instead.
    -   `change_player_name` use `LotgdTool::changePlayerName($newname, $old)` instead.
    -   `change_player_ctitle` use `LotgdTool::changePlayerCtitle($nctitle, $old)` instead.
    -   `change_player_title` use `LotgdTool::changePlayerTitle($ntitle, $old)` instead.
-   **lib/personal_functions.php** Function `killplayer` use `LotgdKernel::get('lotgd_core.tool.staff')->killPlayer($explossproportion, $goldlossproportion)` instead.
-   **lib/holiday_texts.php** Function `holidayize` use `LotgdTool::holidayize($text, $type)` instead.

### :wrench: FIXES

-   Nothing

### :x: REMOVES

-   **BC** **public/battle.php** Removed, use `LotgdKernel::get('lotgd_core.combat.battle')` instead
    -   Example in `public/forest.php`
-   **BC** `src/core/Navigation/Navigation.php` method `fightNav` use `LotgdKernel::get('lotgd_core.combat.battle')->fightNav()` instead
    -   See in `public/forest.php`
-   **BC** Delete `lib/e_dom.php` not used by core.
-   **BC** **Remove deprecated**
    -   **lib/addnews.php** Removed deprecated function `addnews`, removed file too.
    -   **lib/battle/** Removed all files and functions 
        -   **lib/battle/buffs.php**
            -   `activate_buffs`
            -   `process_lifetaps`
            -   `process_dmgshield`
            -   `expire_buffs`
            -   `expire_buffs_afterbattle`
        -   **lib/battle/extended.php**
            -   `prepare_data_battlebars`
            -   `prepare_fight`
            -   `prepare_companions`
            -   `suspend_companions`
            -   `unsuspend_companions`
            -   `autosettarget`
            -   `report_companion_move`
            -   `rollcompaniondamage`
            -   `battle_spawn`
            -   `battle_heal`
            -   `execute_ai_script`
        -   **lib/battle/functions.php**
            -   `battle_player_attacks`
            -   `battle_badguy_attacks`
            -   `battlevictory`
            -   `battlegainexperienceforest`
            -   `battlegainexperiencegraveyard`
            -   `battledefeat`
            -   `battleshowresults`
        -   **lib/battle/skills.php**
            -   `rolldamage`
            -   `report_power_move`
            -   `suspend_buffs`
            -   `suspend_buff_by_name`
            -   `unsuspend_buff_by_name`
            -   `is_buff_active`
            -   `unsuspend_buffs`
            -   `apply_bodyguard`
            -   `apply_skill`
    -   **lib/charcleanup.php** Removed deprecated function `char_cleanup`, removed file too.
    -   **lib/checkban.php** Removed deprecated function `checkban`, removed file too.
    -   **lib/creaturefunctions.php** Removed file and functions:
        -   `lotgd_generate_creature_levels`
        -   `lotgd_generate_doppelganger`
        -   `lotgd_transform_creature`
        -   `lotgd_search_creature`
        -   `get_creature_stats`
        -   `get_creature_hitpoints`
        -   `get_creature_attack`
        -   `get_creature_defense`
        -   `get_creature_speed`
        -   `lotgd_show_debug_creature`
    -   **lib/buffs.php** Removed all files and functions 
        -   `calculate_buff_fields`
        -   `restore_buff_fields`
        -   `apply_buff`
        -   `apply_companion`
        -   `strip_buff`
        -   `strip_all_buffs`
        -   `has_buff`
    -   **lib/datetime.php** Remove file and functions
        -   `checkday`
        -   `is_new_day`
        -   `getgametime`
        -   `gametime`
        -   `convertgametime`
        -   `gametimedetails`
        -   `secondstonextgameday`
    -   **lib/deathmessage.php** Removed deprecated function `select_deathmessage`, removed file too.
    -   **lib/debuglog.php** Removed deprecated function `debuglog`, removed file too.
    -   **lib/experience.php** Removed deprecated function `exp_for_next_level`, removed file too.
    -   **lib/fightnav.php** Removed deprecated function `fightnav`, removed file too.
    -   **lib/forestoutcomes.php** Removed deprecated function `buffbadguy`, removed file too.
    -   **lib/gamelog.php** Removed deprecated function `gamelog`, removed file too.
    -   **lib/increment_specialty.php** Removed deprecated function `increment_specialty`, removed file too.
    -   **lib/playerfunctions.php** Removed all functions and file
        -   `get_player_hitpoints`
        -   `explained_get_player_hitpoints`
        -   `get_player_attack`
        -   `explained_row_get_player_attack`
        -   `explained_get_player_attack`
        -   `get_player_defense`
        -   `explained_row_get_player_defense`
        -   `explained_get_player_defense`
        -   `get_player_speed`
        -   `get_player_physical_resistance`
    -   **lib/saveuser.php** Removed deprecated function `saveuser`, removed file too.
    -   **lib/settings.php** Removed all functions and file
        -   `savesetting`
        -   `clearsettings`
        -   `getsetting`
        -   `getallsettings`
    -   **lib/substitute.php** Removed all functions and file
        -   `substitute`
        -   `substitute_array`
    -   **lib/taunt.php** Removed deprecated function `select_taunt`, removed file too.
    -   **lib/tempstat.php** Removed all functions and file
        -   `apply_temp_stat`
        -   `check_temp_stat`
        -   `suspend_temp_stats`
        -   `restore_temp_stats`
    -   **src/core/EntityRepository/** Removed deprecated classes in folder. Use `Lotgd\Core\Repository\**`

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
