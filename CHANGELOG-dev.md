# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/migration/README.md).   
Visit **_latest_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG.md)  
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V3.md)  
Visit **_V4_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V4.md)  

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
