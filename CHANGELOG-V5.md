# History of changes for IDMarinas Edition

This is a list of changes made in versions __5.*.*__


# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/migration/README.md).   
Visit **_DEV_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-dev.md)  
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V3.md)  
Visit **_V4_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V4.md)  

# Version: 5.5.9 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **src/core/Installer/Pattern/Version.php** Fix error, with version `50105` now have correct name `5.1.5 IDMarinas Edition`
-   **lib/serverfunctions.class.php** Fix error with `$query` replace `addWhere` for `andWhere`, replace `setParamater` for `setParameter`
 
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

# Version: 5.5.8 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **src/core/Controller/InnController.php** Fix error, now use correct redirection

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

# Version: 5.5.7 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **themes/LotgdTheme/templates/admin/page/donators.html.twig** 
    -   Fix error with template path
    -   Fix error with template extension for partials

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

# Version: 5.5.6 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **src/core/Repository/LogdnetRepository.php** 
    -   Fix error, method `getDoctrine` not exist in class
-   **src/core/Repository/AccountsOutputRepository.php** Fix error, Import Tracy Debugger class
-   **src/core/Repository/ArmorRepository.php** Fix error, Import Tracy Debugger class

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

# Version: 5.5.5 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **src/core/Output/Commentary.php** 
    -   Fix error, now pass array to modulehook `moderate-comment-sections`
    -   Fix error, substr length is 2 and not 1 in `processSpecialCommands` for `::` command

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

# Version: 5.5.4 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **lib/pvpsupport.php** 
    -   Function: `setup_pvp_target`
        -   Fix error with translation key (now use correct)
        -   Not need extractEntity is an array
        -   Fix error with compare to dates
    -   Function: `pvpvictory` and `pvpdefeat`
        -   Fixed error with translation keys
    -   Function: `pvpvictory`
        -   Now select correct max experience to lost
-   **src/core/Repository/CharactersRepository.php** Function `getCharacterForPvp` Fix error, now add `character_id` to array
-   **public/train.php** Fixed error by not allowed multiple challange to master when allow by config.
-   **public/referers.php** Delete `require_once 'lib/dhms.php';` file not exists.
-   **public/debug.php** Delete `require_once 'lib/dhms.php';` file not exists.
-   **public/healer.php** Fix error, now pass var `params` to controller.
-   **src/core/Controller/HealerController.php** Use correct format for access value of index of array
-   **translations/en/partial_taunt+intl-icu.en.yaml** Fix error with var names

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

# Version: 5.5.3 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **public/forest.php** Fix error by show normal navs when see Dragon
-   **translations/en/navigation_newday+intl-icu.yaml** and **translations/en/page_newday+intl-icu.yaml** Add translation for ff type key

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

# Version: 5.5.2 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **src/core/Controller/HofController.php** Fixed: now use method and not global function.
-   Fixed error in some configuration pages:
    -   Related to try duplicate user
    -   Change `\Doctrine::clear();` to `\Doctrine::detach($entity);`
    -   **Files affected**
        -   `lib/configuration/cronjob.php `
        -   `public/armoreditor.php`
        -   `public/companions.php`
        -   `public/creatures.php`
        -   `public/masters.php`
        -   `public/mounts.php`
        -   `public/titleedit.php`
        -   `public/user.php`
        -   `public/weaponeditor.php`

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

# Version: 5.5.1 

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **lib/modules/injectmodule.php** Fixed: return false when not have the name of module.
-   **src/core/Controller/MercenaryCampController.php** Fixed: method `buy` now get `$session` global var.
-   **src/core/Command/LotgdIntallCommand.php** and **src/core/Command/UserCreateCommand.php** Replace unexist method in class `Symfony\Component\Console\Style\SymfonyStyle` 
-   **src/core/Command/UserCreateCommand.php** Fixed error with constraints
-   **themes/LotgdTheme/templates/admin/page/about/_partial.html.twig** Fix error with url of download a bundle. Remove escape filter of url.
-   **public/about.php** Fix error in secuence of if

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

# Version: 5.5.0 

### :cyclone: CHANGES

-   This pages use a controller to render content of page
    -   `public/forest.php` use controller `Lotgd\Core\Controller\ForestController`
-   **src/core/Twig/Extension/GameCore.php** Moved date time function to a new Twig Extension
-   **Entity Repositories** Moved to new location:
    -   From `src/core/EntityRepository/` to `src/core/Repository/` 
        -   Added transition classes in `src/core/EntityRepository/` to avoid BC, this clases will removed in version 6.0.0.
-   **Battle** 
    -   Refactor Battle to use services. See `public/battle.php`
-   **THEME**
    -   Updated Fomantic UI version: 2.8.7 => 2.8.8

### :star: FEATURES

-   **Theme/Template**
    -   Add **Sonata Block Event** to some templates:
        -   **themes/LotgdTheme/templates/_layout.html.twig** 
            -   Add sonata block event `lotgd_core.paypal`  
                -   Example of usage in `src/core/Service/PaypalButtons` and see service `lotgd_core.service.paypal.buttons` in `config/service.yaml`
        -   Add new Sonata Block Events: 
            -   **themes/LotgdTheme/templates/page/home.html.twig**
                -   `lotgd.core.page.home.pre` avoid using `includeTemplatesPre` in `$params`
                -   `lotgd.core.page.home.index` avoid using `includeTemplatesIndex` in `$params`
                -   `lotgd.core.page.home.text` avoid using `hookHomeText` in `$params`
                -   `lotgd.core.page.home.middle` avoid using `hookHomeMiddle` in `$params`
                -   `lotgd.core.page.home.post` avoid using `includeTemplatesPost` in `$params`
-   **Page About** Add new section "Bundle Info" like "Module Info" but for Bundles

### :fire: DEPRECATED

-   **lib/battle/** All files and functions (removed in future version)
    -   **lib/battle/buffs.php**
        -   `activate_buffs` use `LotgdKernel::get('lotgd_core.combat.battle')->activateBuffs($tag)` instead
        -   `process_lifetaps` use `LotgdKernel::get('lotgd_core.combat.battle')->processLifeTaps($ltaps, $damage)` instead
        -   `process_dmgshield` use `LotgdKernel::get('lotgd_core.combat.battle')->processDmgShield($dshield, $damage)` instead
        -   `expire_buffs` use `LotgdKernel::get('lotgd_core.combat.battle')->expireBuffs()` instead
        -   `expire_buffs_afterbattle` use `LotgdKernel::get('lotgd_core.combat.battle')->expireBuffsAfterBattle()` instead
    -   **lib/battle/extended.php**
        -   `prepare_data_battlebars` use `LotgdKernel::get('lotgd_core.combat.battle')->prepareDataBattleBars($enemies)` instead
        -   `prepare_fight` use `LotgdKernel::get('lotgd_core.combat.battle')->prepareFight($options)` instead
        -   `prepare_companions` use `LotgdKernel::get('lotgd_core.combat.battle')->prepareCompanions()` instead
        -   `suspend_companions` use `LotgdKernel::get('lotgd_core.combat.battle')->suspendCompanions($susp, $nomsg)` instead
        -   `unsuspend_companions` use `LotgdKernel::get('lotgd_core.combat.battle')->unSuspendCompanions($susp, $nomsg)` instead
        -   `autosettarget` use `LotgdKernel::get('lotgd_core.combat.battle')->autoSetTarget($localenemies)` instead
        -   `report_companion_move` use `LotgdKernel::get('lotgd_core.combat.battle')->reportCompanionMove($companion, $activate)` instead
        -   `rollcompaniondamage` use `LotgdKernel::get('lotgd_core.combat.battle')->rollCompanionDamage($companion)` instead
        -   `battle_spawn` use `LotgdKernel::get('lotgd_core.combat.battle')->battleSpawn($creature)` instead
        -   `battle_heal` use `LotgdKernel::get('lotgd_core.combat.battle')->battleHeal($amount, $target)` instead
        -   `execute_ai_script` use `LotgdKernel::get('lotgd_core.combat.battle')->executeAiScript($script)` instead
    -   **lib/battle/functions.php**
        -   `battle_player_attacks` use `LotgdKernel::get('lotgd_core.combat.battle')->battlePlayerAttacks()` instead
        -   `battle_badguy_attacks` use `LotgdKernel::get('lotgd_core.combat.battle')->battleBadguyAttacks()` instead
        -   `battlevictory` use `LotgdKernel::get('lotgd_core.combat.battle')->battleVictory($enemies, $denyflawless, $forest)` instead
        -   `battlegainexperienceforest` use `LotgdKernel::get('lotgd_core.combat.battle')->battleGainExperienceForest()` instead
        -   `battlegainexperiencegraveyard` use `LotgdKernel::get('lotgd_core.combat.battle')->battleGainExperienceGraveyard()` instead
        -   `battledefeat` use `LotgdKernel::get('lotgd_core.combat.battle')->battleDefeat($enemies, $where, $candie, $lostexp, $lostgold)` instead
        -   `battleshowresults` use `LotgdKernel::get('lotgd_core.combat.battle')->battleShowResults($lotgdBattleContent)` instead
    -   **lib/battle/skills.php**
        -   `rolldamage` use `LotgdKernel::get('lotgd_core.combat.battle')->rollDamage()` instead
        -   `report_power_move` use `LotgdKernel::get('lotgd_core.combat.battle')->reportPowerMove($crit, $dmg)` instead
        -   `suspend_buffs` use `LotgdKernel::get('lotgd_core.combat.battle')->suspendBuffs($susp, $msg)` instead
        -   `suspend_buff_by_name` use `"LotgdKernel::get('lotgd_core.combat.battle')->suspendBuffByName($name, $msg)` instead
        -   `unsuspend_buff_by_name` use `LotgdKernel::get('lotgd_core.combat.battle')->unsuspendBuffByName($name, $msg)` instead
        -   `is_buff_active` use `LotgdKernel::get('lotgd_core.combat.battle')->isBuffActive($name)` instead
        -   `unsuspend_buffs` use `LotgdKernel::get('lotgd_core.combat.battle')->unsuspendBuffs($susp, $msg)` instead
        -   `apply_bodyguard` use `LotgdKernel::get('lotgd_core.combat.battle')->applyBodyguard($level)` instead
        -   `apply_skill` use `LotgdKernel::get('lotgd_core.combat.battle')->applySkill($skill, $l)` instead
-   **lib/buffs.php** All functions (removed in future version)
    -   `calculate_buff_fields` use `LotgdKernel::get('lotgd_core.combat.buffer')->calculateBuffFields()` instead
    -   `restore_buff_fields` use `LotgdKernel::get('lotgd_core.combat.buffer')->restoreBuffFields()` instead
    -   `apply_buff` use `LotgdKernel::get('lotgd_core.combat.buffer')->applyBuff($name, $buff)` instead
    -   `apply_companion` use `LotgdKernel::get('lotgd_core.combat.buffer')->applyCompanion($name, $companion, $ignorelimit)` instead
    -   `strip_buff` use `LotgdKernel::get('lotgd_core.combat.buffer')->stripBuff($name)` instead
    -   `strip_all_buffs` use `LotgdKernel::get('lotgd_core.combat.buffer')->stripAllBuffs()` instead
    -   `has_buff` use `LotgdKernel::get('lotgd_core.combat.buffer')->hasBuff($name)` instead
-   **lib/tempstat.php** All functions (removed in future version)
    -   `apply_temp_stat` use `LotgdKernel::get('lotgd_core.combat.temp_stats')->applyTempStat($name, $value, $type)` instead
    -   `check_temp_stat` use `LotgdKernel::get('lotgd_core.combat.temp_stats')->checkTempStat($name, $color)` instead
    -   `suspend_temp_stats` use `LotgdKernel::get('lotgd_core.combat.temp_stats')->suspendTempStats()` instead
    -   `restore_temp_stats` use `LotgdKernel::get('lotgd_core.combat.temp_stats')->restoreTempStats()` instead
-   **lib/fightnav.php** `fightnav` use `LotgdNavigation::fightNav($allowspecial, $allowflee, $script)` instead
-   **lib/datetime.php** All functions (removed in future version)
    -   `checkday` use `LotgdKernel::get('lotgd_core.tool.date_time')->checkDay()` instead
    -   `is_new_day` use `LotgdKernel::get('lotgd_core.tool.date_time')->isNewDay()` instead
    -   `getgametime` use `LotgdKernel::get('lotgd_core.tool.date_time')->getGameTime()` instead
    -   `gametime` use `LotgdKernel::get('lotgd_core.tool.date_time')->gameTime()` instead
    -   `convertgametime` use `LotgdKernel::get('lotgd_core.tool.date_time')->convertGameTime($intime, $debug)` instead
    -   `gametimedetails` use `LotgdKernel::get('lotgd_core.tool.date_time')->gameTimeDetails()` instead
    -   `secondstonextgameday` use `LotgdKernel::get('lotgd_core.tool.date_time')->secondsToNextGameDay($details)` instead
-   **lib/increment_specialty.php** `increment_specialty` use `LotgdKernel::get('lotgd_core.tool.player_functions')->incrementSpecialty($colorcode, $spec)` instead
-   **lib/playerfunctions.php** All functions (removed in future version)
    -   `get_player_hitpoints` use `LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerHitpoints($player)` instead
    -   `explained_get_player_hitpoints` use `LotgdKernel::get('lotgd_core.tool.player_functions')->explainedGetPlayerHitpoints($player, $colored)` instead
    -   `get_player_attack` use `LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerAttack($player)` instead
    -   `explained_row_get_player_attack` use `LotgdKernel::get('lotgd_core.tool.player_functions')->explainedRowGetPlayerAttack($player)` instead
    -   `explained_get_player_attack` use `LotgdKernel::get('lotgd_core.tool.player_functions')->explainedGetPlayerAttack($player, $colored)` instead
    -   `get_player_defense` use `LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerDefense($player)` instead
    -   `explained_row_get_player_defense` use `LotgdKernel::get('lotgd_core.tool.player_functions')->explainedRowGetPlayerDefense($player)` instead
    -   `explained_get_player_defense` use `LotgdKernel::get('lotgd_core.tool.player_functions')->explainedGetPlayerDefense($player, $colored)` instead
    -   `get_player_speed` use `LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerSpeed($player)` instead
    -   `get_player_physical_resistance` use `LotgdKernel::get('lotgd_core.tool.player_functions')->getPlayerPhysicalResistance($player)` instead
-   **lib/creaturefunctions.php** All functions (removed in future version)
    -   `lotgd_generate_creature_levels` use `LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdGenerateCreatureLevels($level)` instead
    -   `lotgd_generate_doppelganger` use `LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdGenerateDoppelganger($level)` instead
    -   `lotgd_transform_creature` use `LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdTransformCreature($badguy, $debug)` instead
    -   `lotgd_search_creature` use `LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdSearchCreature($multi, $targetlevel, $mintargetlevel, $packofmonsters, $forest)` instead
    -   `get_creature_stats` use `LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureStats($dk)` instead
    -   `get_creature_hitpoints` use `LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureHitpoints($attrs)` instead
    -   `get_creature_attack` use `LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureAttack($attrs)` instead
    -   `get_creature_defense` use `LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureDefense($attrs)` instead
    -   `get_creature_speed` use `LotgdKernel::get('lotgd_core.tool.creature_functions')->getCreatureSpeed($attrs)` instead
    -   `lotgd_show_debug_creature` use `LotgdKernel::get('lotgd_core.tool.creature_functions')->lotgdShowDebugCreature($badguy)` instead
-   **lib/settings.php** All functions (removed in future version)
    -   `savesetting` use `LotgdSetting::saveSetting($settingname, $value)` instead
    -   `clearsettings` use `LotgdSetting::clearSettings()` instead
    -   `getsetting` use `LotgdSetting::getSetting($settingname, $default)` instead
    -   `getallsettings` use `LotgdSetting::getAllSettings()` instead

### :wrench: FIXES

-   **themes/LotgdTheme/templates/page/_blocks/_newday.html.twig** Now pass mount name if mount give turns.
-   **public/images/logdnet.php** Fixed error with required file
-   **src/core/Controller/LogdnetController.php** 
    -   Fix error, use correct path of image.
    -   Fix: Not overwrite $response var (with response of http get client)
    -   Fix: use $session var and not getUser() method

### :x: REMOVES

-   **BC** **Remove script of Cookie Consent** only need in EU territory, if you need can use one of this:
    -   https://github.com/kiprotect/klaro
    -   https://github.com/nucleos/NucleosGDPRBundle
    -   https://github.com/osano/cookieconsent/

### :notebook: NOTES

-   Make some optimizations in files. (Code Smell/Duplicated)
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

# Version: 5.4.3

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **themes/LotgdTheme/templates/page/_blocks/_inn.html.twig** Fixed error, pass necesary vars to translation

### :x: REMOVES

-   Nothing

### :notebook: NOTES

-   **Notes**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

# Version: 5.4.2

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **src/core/Event/EveryRequest.php** Fixed error with values of const 
-   **src/core/Form/ConfigurationType/CommentaryType.php** Fixed error: removed usage of `Symfony\Component\Validator\Constraints\AtLeastOneOf` not exist in version 4.4 of Symfony
-   **src/core/Fixed/EventDispatcher.php** Delete typed for param in method `instance` in production pass diferent class.
-   **lib/battle/functions.php** Fix error when "die" in graveyard, now show message and nav

### :x: REMOVES

-   Nothing

### :notebook: NOTES

-   **Notes**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

# Version: 5.4.1

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **src/core/EntityRepository/ModulesRepository.php** Fixed error when reinstall a module. (Invalid date format)
-   **src/core/Controller/AboutController.php** Fixed error, defined name for cache key.

### :x: REMOVES

-   Nothing

### :notebook: NOTES

-   **Notes**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

# Version: 5.4.0

### :cyclone: CHANGES

-   This pages use a controller to render content of page
    -   `public/account.php` use controller `Lotgd\Core\Controller\AccountController`
    -   `public/armor.php` use controller `Lotgd\Core\Controller\ArmorController`
    -   `public/bank.php` use controller `Lotgd\Core\Controller\BankController`
    -   `public/clan.php` use controller `Lotgd\Core\Controller\ClanController`
    -   `public/create.php` use controller `Lotgd\Core\Controller\CreateController`
    -   `public/gardens.php` use controller `Lotgd\Core\Controller\GardenController`
    -   `public/gypsy.php` use controller `Lotgd\Core\Controller\GypsyController`
    -   `public/healer.php` use controller `Lotgd\Core\Controller\HealerController`
    -   `public/hof.php` use controller `Lotgd\Core\Controller\HofController`
    -   `public/inn.php` use controller `Lotgd\Core\Controller\InnController`
    -   `public/lodge.php` use controller `Lotgd\Core\Controller\LodgeController`
    -   `public/mercenarycamp.php` use controller `Lotgd\Core\Controller\MercenaryCampController`
    -   `public/news.php` use controller `Lotgd\Core\Controller\NewsController`
    -   `public/referral.php` use controller `Lotgd\Core\Controller\ReferralController`
    -   `public/rock.php` use controller `Lotgd\Core\Controller\RockController`
    -   `public/shades.php` use controller `Lotgd\Core\Controller\ShadesController`
    -   `public/stables.php` use controller `Lotgd\Core\Controller\StableController`
    -   `public/village.php` use controller `Lotgd\Core\Controller\VillageController`
    -   `public/weapons.php` use controller `Lotgd\Core\Controller\WeaponController`
    -   Note: See this files for an examples of how to use the controllers in LoTGD Core.
-   **Twig Extensions**
    -   `GameCore` Settings and Censor functions are moved to new extensions.
        -   `CensorExtension`
        -   `SettingsExtension`

### :star: FEATURES

-   **Test** add a basic test, limited to any functions of Twig Extensions

### :fire: DEPRECATED

-   **lib/saveuser.php** Function `saveuser` is deprecated and removed in future versions.
    -   Use `LotgdTool::saveUser($update_last_on)` instead or use a dependency injection.
-   **lib/deathmessage.php** Function `select_deathmessage` is deprecated and removed in future versions.
    -   Use `LotgdLog::selectDeathMessage($zone, $extraParams)` instead or use a dependency injection.
-   **lib/taunt.php** Function `select_taunt` is deprecated and removed in future versions.
    -   Use `\LotgdTool::selectTaunt($extraParams)` instead or use a dependency injection.
-   **lib/substitute.php** 
    -   Function `substitute` is deprecated and removed in future versions.
        -   Use `\LotgdTool::substitute($string, $extra, $extrarep)` instead or use a dependency injection.
    -   Function `substitute_array` is deprecated and removed in future versions.
        -   Use `\LotgdTool::substituteArray($string, $extra, $extrarep)` instead or use a dependency injection.

### :wrench: FIXES

-   Nothing

### :x: REMOVES

-   **Deleted some old files** this files is related to version 1.1.2 or older.
    -   `README.txt`
    -   `AFTERUPGRADE.txt`
    -   `BUG FIXES.txt`
-   **Deleted** all files in folder `lib/clan/`:
    -   `applicant_apply.php` 
    -   `applicant_new.php` 
    -   `applicant.php` 
    -   `clan_default.php` 
    -   `clan_membership.php` 
    -   `clan_motd.php` 
    -   `clan_withdraw.php` 
    -   `detail.php` 
    -   `list.php` 
-   **Deleted** file `lib/inn/inn_bartender.php`

### :notebook: NOTES

-   **Notes**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

# Version: 5.3.0

### :cyclone: CHANGES

-   This pages use a controller to render content of page
    -   `public/about.php` use controller `Lotgd\Core\Controller\AboutController`
    -   `public/home.php` use controller `Lotgd\Core\Controller\HomeController`
    -   `public/list.php` use controller `Lotgd\Core\Controller\ListController`
    -   Note: See this files for an examples of how to use the controllers in LoTGD Core.
-   **Translations**
    -   `translations/en/navigation_stables+intl-icu.en.yaml` Add new translations

### :star: FEATURES

-   **Add** new events to Event Dispatcher.
    -   See class `Lotgd\Core\Events` and `src/core/Event/` folder for all events.
-   **Controllers** First approach to controllers in LoTGD Core
    -   `public/home.php` Have the first controller of LoTGD Core
        -   Can see this example for use in your modules. Is a good step to full migrate modules to bundles.

### :fire: DEPRECATED

-   **lib/gamelog.php** Function `gamelog` is deprecated and removed in future versions.
    -   Use `LotgdLog::game(string $message, string $category)` instead or use a dependency injection.
-   **lib/debuglog.php** Function `debuglog` is deprecated and removed in future versions.
    -   Use `LotgdLog::debug(string $message, ?int $target = null, ?int $user = null, ?string $field = null, ?int $value = null, bool $consolidate = true)` instead or use a dependency injection.
-   **lib/addnews.php** Function `addnews` is deprecated and removed in future versions.
    -   Use `LotgdTool::addNews(string $text, array $params, string $textDomain, bool $hideFromBio);` instead or use a dependency injection.
-   **lib/experience.php** Function `exp_for_next_level` is deprecated and removed in future versions.
    -   Use `LotgdTool::expForNextLevel($curlevel, $curdk);` instead or use a dependency injection.
-   **lib/lotgd_mail.php** Function `lotgd_mail` is deprecated and removed in future versions.
    -   Use `Symfony mailer` instead.
-   **lib/checkban.php** Function `checkban` is deprecated and removed in future versions.
    -   Use `LotgdLog::checkBan(?string $login)` instead or use a dependency injection.
-   **lib/charcleanup.php** Function `char_cleanup` is deprecated and removed in future versions.
    -   Functions `createBackupOfEntity` and `createBackupBasicInfo` are used by `char_cleanup` and removed from file.
    -   Use `LotgdKernel::get(\Lotgd\Core\Tool\Backup::class)->characterCleanUp($accountId, $type)` instead or use a dependency injection.
        -   `LotgdKernel::get('lotgd.core.backup')->characterCleanUp($accountId, $type)`.
    
### :wrench: FIXES

-   **public/stables.php** Now translate navs for examine mount

### :x: REMOVES

-   **Remove** file `error_docs/dberror.html` not in use.
-   **BC** `public/battle.php` 
    -   Removed the following `modulehooks`
        -   `battle-victory`
        -   `battle-defeat`
    -   Note: this hooks is individual for each creature, better use:
        - Event Dispatcher: 
            -   `Lotgd\Core\Events::PAGE_BATTLE_END_VICTORY`
            -   `Lotgd\Core\Events::PAGE_BATTLE_END_DEFEAT`
        -   Module hook: (this method is deprecated use Event Dispatcher instead)
            -   `battle-victory-end`
            -   `battle-defeat-end`

### :notebook: NOTES

-   **Notes**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

# Version: 5.2.0

### :cyclone: CHANGES

-   **Downgrade** Symfony components from 5.2 versions to 4.4 versions.
-   **Fixed Classes** 
    -   All fixed classes in `src/core/Fixed/` folder.
    -   Instead use this clases `LotgdResponse`, `LotgdRequest`...
    -   Try to use service injection when you can. All this clases is for transiction.

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   All pattern class in **src/core/Pattern/** folder are deprecated. If possible use Dependency Injection.
    -   All these patterns will be removed in future version.

### :wrench: FIXES

-   Nothing

### :x: REMOVES

-   **BC** Delete `AdvertisingBundle` from Core. Now is a independent bundle (can use in other Symfony projects).
    -   If you need/want use in your server can use https://github.com/idmarinas/advertising-bundle
-   **BC** Removed Laminas Event Manager (Lotgd\Core\EventManager\EventManager), now use Symfony Event Dispatcher.
    -   All events triggers are migrated to `evenDispacher->dispatch()`
    -   You can see all available events in `src/core/Event/` folder
    -   Also removed all related files to Hook manager like (`Lotgd\Core\Fixed\HookManager` as `LotgdHook`)
        -   Use `LotgdEventDispatcher` instead of `LotgdHook`
    -   Use Event Dispatcher service for all events. Can create new custom events.
        -   Use `LotgdKernel::get('event_dispatcher')` for get service of Event Dispatcher.
-   **BC** Deleted pattern `Lotgd\Core\Pattern\HookManager`
-   **BC** Deleted events class `Lotgd\Core\Hook` See `src/core/Event/` folder to see all new events.
-   **Installer delete this files/folders**:
    -   `/bin/lotgd`
    -   `/data/form/core/`
    -   `/src/core/Battle/`
    -   `/src/core/Component/`
    -   `/src/core/Console/`
    -   `/src/core/Factory/`
    -   `/src/core/Filter/`
    -   `/src/core/Db/`
    -   `/src/core/Nav/`
    -   `/src/core/Patern/`
    -   `/src/core/Translator/`
    -   `/src/core/Validator/`
    -   `/src/core/Template/Base.php`
    -   `/src/core/Template/Theme.php`
    -   `/src/core/EventManagerAware.php`
    -   `/src/core/Event.php`
    -   `/src/core/Http.php`
    -   `/src/core/Modules.php`
    -   `/src/core/ServiceManager.php`
    -   `/src/core/Session.php`
    -   `/src/core/Fixed/Cache.php`
    -   `/src/core/Fixed/Dbwrapper.php`
    -   `/src/core/Fixed/EventManager.php`
    -   `/src/core/Fixed/Format.php`
    -   `/src/core/Fixed/Http.php`
    -   `/src/core/Fixed/HookManager.php`
    -   `/src/core/Fixed/Locator.php`
    -   `/src/core/Fixed/SymfonyForm.php`
    -   Note: all this files/folders are automatically deleted when upgrade to version 5.2.0, this files not are in used by the LoTGD Core.

### :notebook: NOTES

-   **Notes**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   Read info in `var/log/*` files, and see the problem.
        -   Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS distribution) in your webserver.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

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
        │   └── asset.jpg
        ├── templates
        │   ├── bundles
        │   │   └── AcmeBundle
        │   │       └── bundleTemplate.html.twig
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

### :x: REMOVES

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

### :x: REMOVES

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
