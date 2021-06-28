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

-   **lib/buffs.php** All functions (removed in future version)
    -   `calculate_buff_fields` use `LotgdKernel::get('lotgd_core.combat.buffs')->calculateBuffFields()` instead
    -   `restore_buff_fields` use `LotgdKernel::get('lotgd_core.combat.buffs')->restoreBuffFields()` instead
    -   `apply_buff` use `LotgdKernel::get('lotgd_core.combat.buffs')->applyBuff($name, $buff)` instead
    -   `apply_companion` use `LotgdKernel::get('lotgd_core.combat.buffs')->applyCompanion($name, $companion, $ignorelimit)` instead
    -   `strip_buff` use `LotgdKernel::get('lotgd_core.combat.buffs')->stripBuff($name)` instead
    -   `strip_all_buffs` use `LotgdKernel::get('lotgd_core.combat.buffs')->stripAllBuffs()` instead
    -   `has_buff` use `LotgdKernel::get('lotgd_core.combat.buffs')->hasBuff($name)` instead
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