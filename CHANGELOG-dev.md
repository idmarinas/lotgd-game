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

-   Nothing

### :star: FEATURES

-   **Stmimulus "route"**
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

-   Nothing

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
