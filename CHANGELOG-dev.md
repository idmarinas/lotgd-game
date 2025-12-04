# Changes of LoTGD IDMarinas Edition

Visit the [Wiki](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [Documentation](https://idmarinas.github.io/lotgd-game/) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/migration/README.md).  
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/7.2/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/7.2/CHANGELOG-V3.md)  
Visit **_V4_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/7.2/CHANGELOG-V4.md)  
Visit **_V5_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/7.2/CHANGELOG-V5.md)  
Visit **_V6_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/7.2/CHANGELOG-V6.md)  
Visit **_V7_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/7.2/CHANGELOG-V7.md)

# Version: 7.2.0

### :cyclone: CHANGES

- **Version show** now shows an original version of LoTGD followed by a custom version of IDMarinas Edition
- **Refactor** a refactor has been made to all the code to migrate to the `PHP 8.0` version.

### :star: FEATURES

- **src/functions.php**
  - `output()` and `rawoutput()` added for compatibility with old modules, this makes it necessary to modify
    the old modules less.
- **Docker** added Docker-related files to containerized LoTGD
  - Docker configuration has been created for **Dev** and **Prod**.
    - It is advisable to **review** the configuration before using it in production.
  - The configuration to use Caddy-FrankenPHP instead of Apache is also included. By default, Caddy-FrankenPHP is used

### :fire: DEPRECATED

- Nothing

### :wrench: FIXES

- **public/login.php** Fixed typo in `LotgdMessaged::` rename to `LotgdMessages::`
- **Recover password system** Fixed a bug where the password could not be reset.
- **lib/redirect.php** Fixed a possible bug: now `REQUEST_SCHEME` and not `SERVER_PORT` is used to construct the url
- **Petition View**: now shows the type of petition
- **Styling**
  - Fixed the styling of some templates.
- **Fixes** some accessibility problems have been corrected

### :x: REMOVES

- **Jaxon-PHP** removed, migrated to StimulusJS
  - **Removed files**
    - **src/core/Twig/Extension/Jaxon.php**
    - **src/core/Service/Jaxon.php**
    - **src/core/Jaxon/Library/Semantic/Modal.php**
    - **src/core/Service/Jaxon.php**
    - **src/core/AjaxAbstract.php**
- **BC** remove file **lib/serverfunctions.class.php**
  - `isTheServerFull()` use `LotgdKernel::get("lotgd_core.service.server_functions")->isTheServerFull()` instead or
    dependency injection.
  - `resetAllDragonkillPoints($acctid))` use
    `LotgdKernel::get("lotgd_core.service.server_functions")->resetAllDragonkillPoints($acctid))` instead or
    dependency injection.
- **BC** Removed the following packages in `composer.json`
  - `laminas/laminas-log` dependency
  - `symfony/debug-bundle` dependency (not used)
  - `laminas/laminas-math` dependency
  - `laminas/laminas-validator` dependency
  - `jaxon-php/jaxon-dialogs` dependency
  - `jaxon-php/jaxon-core` dependency

### :notebook: NOTES

- **Important**:
  - :warning: Since version 5.0.0 Installer is only via terminal (command: `php bin/console lotgd:install`)
  - :warning: Avoid, as far as possible, using static classes (e.g., LotgdSetting, Doctrine, LotgdTranslation...) as
    these classes will be deleted in a future version. Use autowire, dependency injection when possible.
  - :warning: Version 7.0.0 changes templates for use **TailwindCSS**
- **Upgrade/Install for version 5.0.0 and up**
  - First, read [docs](https://github.com/idmarinas/lotgd-game/wiki/Skeleton) and follow steps.
  - If you have problems:
    - Read info in `storage/log/tracy/*` files, and see the problem.
    - Read info in `var/log/*` files, and see the problem.
    - Read info in `var/log/apache2/error.log` (this is the default location in Debian, can change in your OS
      distribution) in your webserver.
    - If you can't solve the problem, go to: [Repository issues](https://github.com/idmarinas/lotgd-game/issues)
- **composer.json** Updated/Added/Deleted dependencies
- **package.json** Updated/Added/Deleted dependencies
