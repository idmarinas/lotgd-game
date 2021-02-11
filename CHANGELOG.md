# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/master/README.md).   
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V3.md)  
Visit **_V4_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V4.md)  
Visit **_V5_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V5.md)  

# Version: 6.0.0

### :cyclone: CHANGES

-   **Battle Script**
    -   **First** iteration of changes in Battle system.
        -   Moved some part of Battle System to a Service `Lotgd\Core\Combat\Battle`
            -   Get Battle service with `LotgdKernel::get(Lotgd\Core\Combat\Battle::class)`
    -   No need more require `battle.php` file.
        -   Only need:
            ```php
                $battleInstance
                    //-- Configure battle
                    ->setBattleZone('forest') //-- Default is forest.
                    ->enableCreateNews() //-- Default is enable
                    ->enableLostGold() //-- Default is enable
                    ->enableLostExp() //-- Default is enable
                    ->enableDie() //-- Default is enable
                    ->enableFlawless() //-- Default is enable
                    ->enableVictoryDefeat() //-- Default is enable. Disable if you want simulate battle

                    //-- Start battle
                    ->battleStart($companions, $session['user'], $session['buffslist'] ?? [])

                    //-- Process the battle
                    ->battleProcess()

                    //-- End battle
                    ->battleEnd()
                ;
            ```

### :star: FEATURES

-   Nothing

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **lib/modules/objectpref.php** Fixed error: now use same cache service. So not give problems with get/set object prefs
-   **src/core/Form/ConfigurationType/TrainingType.php** Fixed error with translation keys.
-   **src/core/Http/Response.php**  Fixed errors:
    -   `pageTitle()` Now replace title correctly.
    -   `pageDebug()` Param $text can be mixed
-   **lib/figthnav.php** Fixed, now show name of creature when is target.

### :x: REMOVES/Break Changes

-   Nothing

### :notebook: NOTES

-   **Notes**:
    -   :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
-   **Upgrade/Install for version 5.0.0 and up**
    -   First read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
    -   If have problems:
        -   Read info in `storage/log/tracy/*` files, and see the problem.
        -   If you can't solve the problem go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies
