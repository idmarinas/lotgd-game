# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/migration/README.md).
Visit **_latest_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG.md)
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V2.md)
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V3.md)
Visit **_V4_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V4.md)

# Version: 6.0.0

### :cyclone: CHANGES

-   Nothing

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   Nothing

### :x: REMOVES

-   **Remove deprecated**
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
