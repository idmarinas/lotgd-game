# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/migration/README.md).  
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V3.md)  
Visit **_V4_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V4.md)  
Visit **_V5_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V5.md)  
Visit **_V6_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/migration/CHANGELOG-V6.md)  

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
