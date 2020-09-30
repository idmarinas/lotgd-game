# Changes made for IDMarinas Edition

See CHANGELOG.txt for see changes made for Oliver Brendel +nb Edition

Visit the [Documentation](https://github.com/idmarinas/lotgd-game/wiki) for more details.
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/master/README.md).

# Version: 4.4.0

### :cyclone: CHANGES

-   **public/forest.php** deleted obsolete function, and use new translator system to translate prefix of creature.
-   **Moved _template_ and _translation_ folders** to new location
    -   `data/template/` to `templates/`
    -   `data/translation/` to `translations/`
    -   Note: Both are in root directory, and are in "PLURAL"

### :star: FEATURES

-   **Webpack Encore** Now Core use Symfony Webpack-Encore to build JS and CSS of game.
    -   In this interaction can have many namespaces to build. Encore always build "lotgd" namespace.
        -   Note: Can use `webpack.config.custom-example.js` to create your own namespaces for your project (if you need).
    -   Is a preview of new system of templates and themes of Game Core.
        -   Note: the intention is to make the core more flexible when creating new themes for the game and to be able to create several themes at once.
    -   Note: in the next version the templates and translations will be updated.
-   **Configuration cache** Now can clear Twig template cache.
    -   Note: all buttons have the same effect, emptying the template cache directory.
-   **New Hook system**
    -   Now use _Laminas EventManager_ to trigger hooks. No more `modulehook`.
        -   This method does not use the database to trigger the hooks, so the database load is lightened.
    -   Hook system use _Laminas EventManager_.
    -   To know all the hooks that are implemented in the game see the file **src/core/Hook.php**, this file contains all the hooks that are available in the game core.
    -   This is a first preview of new Hook System. Not all old hooks have a new version. Because in future change how create pages of game.

### :fire: DEPRECATED

-   **lib/datetime.php**
    -   `reltime` use `LotgdFormat::relativedate($indate, $default = 'never')` instead.
-   **lib/pageparts.php**
    -   `popup_header`
    -   `popup_footer`
    -   Note: Use Jaxon-PHP to load modals, alerts... and a little parts of game.
        -   For example, if you need this functions I think is better use Jaxon-PHP for better performance.
-   Class **Lotgd\Core\Http** are deprecated use `Lotgd\Core\Http\Request` instead.
-   Static class **Lotgd\Core\Fixed\Http** aka `LotgdHttp` are deprecated.
    -   Use **Lotgd\Core\Fixed\Request** aka `LotgdRequest` instead.
-   Static class **Lotgd\Core\Fixed\SymfonyForm** aka `LotgdForm` are deprecated.
    -   Get the factory to create a Symfony Form. `LotgdLocator::get('Lotgd\Core\SymfonyForm')`
-   Class **Lotgd\Core\SymfonyForm** are deprecated use `Lotgd\Core\Http\Request` instead.
-   **Old Hooks system**
    -   The old hook system for trigger hooks in game are deprecated.
        -  This is a `modulehook` function and associated.
    -   Note: use New Hook system.
    -   Functions deprecated:
        -   **lib/modules/hook.php**
            -   `modulehook`
            -   `module_wipehooks`
            -   `module_drophook`
            -   `module_addhook`
            -   `module_addhook_priority`
        -   Note: Core trigger new Hook system and them old system.

### :wrench: FIXES

-   **src/ajax/core/Petition.php** Fixed error with undefined vars.
-   **public/mercenarycamp.php** Fixed error with: Call to a member function on array
-   **Forms**
    -   **data/form/core/petition/input.php** Fixed error with text domain
    -   **data/form/core/grotto/configuration/.** Fixed error with range input with float values and diferent locale.
        -   The solution for this is added filter to input, `Laminas\Filter\ToFloat` see in `data/form/core/grotto/configuration/filter/pvp.php`
-   **Translations**
    -   **translations/en/grotto/configuration.yaml** Fixed error, now use new folded Yaml format
    -   **translations/en/form/grotto/configuration.yaml** Fixed error, now use correct index name

### :x: REMOVES

-   **lib/forms.php** Removed unused file.
-   **lib/sanitize.php** Removed file.
    -   Removed functions
        -   `sanitize` use new `LotgdSanitize::fullSanitize($string)` instead
        -   `newline_sanitize` use new `LotgdSanitize::newLineSanitize($string)` instead
        -   `color_sanitize` use new `LotgdSanitize::fullSanitize($string)` instead
        -   `comment_sanitize` has no replacement, new commentary system, sanitize comments by default
        -   `logdnet_sanitize` use new `LotgdSanitize::logdnetSanitize($string)` instead
        -   `full_sanitize` use new `LotgdSanitize::fullSanitize($string)` instead
        -   `cmd_sanitize` use new `LotgdSanitize::cmdSanitize($string)` instead
        -   `prevent_colors` use new `LotgdSanitize::preventLotgdCodes($string)` instead
        -   `modulename_sanitize` use new `LotgdSanitize::moduleNameSanitize($string`) instead
        -   `stripslashes_array` has no replacement
        -   `sanitize_name` use new `LotgdSanitize::nameSanitize($spaceallowed, $inname)` instead.
        -   `sanitize_colorname` use new `LotgdSanitize::colorNameSanitize($spaceallowed, $inname, $admin)` instead
        -   `sanitize_html` use new `LotgdSanitize::htmlSanitize($string)` instead
        -   `sanitize_mb` use new `LotgdSanitize::mbSanitize($string)` instead
    -   Moved temporary this function to `lib/translator.php`, only used by this file.
        -   `translator_uri`
        -   `translator_page`
        -   `comscroll_sanitize`

### :notebook: NOTES

-   **Added file to check requeriments** `lotgd-check-requeriments-4.2.php`
-   **Added lazy services**.
    -   These services are not always necessary, so they are only created the first time they are needed.
        -   `Lotgd\Core\Db\Dbwrapper`
        -   `Lotgd\Core\Installer\Install`
        -   `Lotgd\Core\SymfonyForm`
        -   `doctrine.cli`
        -   `DoctrineORMModule\CliConfigurator`
        -   `InputFilterManager`
        -   `FormAnnotationBuilder`
        -   `FormElementManager`
-   **Jaxon-PHP** Migrating Jaxon-Core from 2.2.* to version 3.2.*
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

# Version: 4.3.0

### :cyclone: CHANGES

-   **Transfer to new Laminas/Symfony Form**
    -   **public/configuration.php** Cronjob
        -   Toggle to use cronjob for new day.
        -   Create/Update a cronjob.
    -   **public/home.php** Select skin.
    -   **public/user.php** The form is separated into two: accounts and characters.
        -   Use Symfony Form to edit this entities.
    -   **public/about.php** The form use the new element ViewOnly to show info.
-   :warning: **Modules** `MODULE_NAME_getmoduleinfo`
    -   Changed the way to add the settings/prefs of a module.
        -   Now can use a Laminas Form.
            ```
            [
                'settings' => 'Lotgd\Local\Form\ModuleName\ModuleNameSettings',
                'prefs-companions' => 'Lotgd\Local\Form\ModuleName\ModuleNamePrefsCompanions',
                'prefs-mounts' => 'Lotgd\Local\Form\ModuleName\ModuleNamePrefsMounts',
                'prefs-creatures' => 'Lotgd\Local\Form\ModuleName\ModuleNamePrefsCreatures',
                'prefs-items' => 'Lotgd\Local\Form\ModuleName\ModuleNamePrefsItems',
                'prefs-city' => 'Lotgd\Local\Form\ModuleName\ModuleNamePrefsCity',
                'prefs-drinks' => 'Lotgd\Local\Form\ModuleName\ModuleNamePrefsDrinks',
            ]
            ```
            -   With Laminas Form can validate/filter all inputs in form.
            -   Note: in a future version of LoTGD Core, the function `lotgd_showform` will be deleted (now is deprecated function).
                -   When this function is removed all the above options will no longer work with the old method. And need use Laminas Form or Symfony Form.
-   :warning: function `module_objpref_edit` changes its behavior:
    -   Now return a string or Laminas Form instance.
    -   Now check if prefs have a Laminas Form format or array format (old).
        -   With the old format it issues a deprecated warning.
    -   Se necesita cambiar en consecuencia el código que hace uso de este resultado. Example moduless `cities`, `drinks` and `inventory`. Can see other examples in core game `companions`, `creatures`

    > Example for process from:
    ```php
        $form = module_objpref_edit('PREF_NAME', $module, $objectId);

        $params['isLaminas'] = $form instanceof Laminas\Form\Form;
        $params['module'] = $module;
        //-- And other params need

        if ($params['isLaminas'])
        {
            $form->setAttribute('action', 'URL_FOR_PROCESS_DATA');
            $params['formTypeTab'] = $form->getOption('form_type_tab');
        }

        if (\LotgdHttp::isPost())
        {
            $post = \LotgdHttp::getPostAll();

            if ($params['isLaminas'])
            {
                $form->setData($post);

                if ($form->isValid()) //-- Check if data is valid
                {
                    $data = $form->getData();

                    process_post_save_data($data, $objectId, $module);
                }
            }
            else
            {
                reset($post);

                process_post_save_data($post, $objectId, $module);
            }
        }

        $params['form'] = $form;

        rawoutput(\LotgdTheme::renderModuleTemplate('path/to/template.twig', $params));

        //-- Function to save data
        function process_post_save_data($data, $id, $module)
        {
            foreach ($data as $key => $val)
            {
                if (is_array($val)) //-- Check for not save an array in pref
                {
                    process_post_save_data($val, $id, $module);

                    continue;
                }

                set_module_objpref('PREF_NAME', $id, $key, $val, $module);
            }
        }
    ```

    > Example of template
    ```html
    {% translate_default_domain textDomain %}

    {% if isLaminas %}
        {% if formTypeTab %}
            {{ laminas_form_tab(form) }}
        {% else %}
            {{ laminas_form(form) }}
        {% endif %}
    {% else %}
        <form action="{{ 'runmodule.php?module=cityprefs&op=editmodulesave&cityid=' ~ cityId ~ '&mdule=' ~ module }}" method="POST" autocomplete="off">
            {{ form }}
        </form>
    {% endif %}
    ````

### :star: FEATURES

-   **Twig Template**
    -   New filters:
        -   `affirmation_negation` or `yes_no`
            -   Default function parameters: `affirmationNegation($value, $yes = 'adverb.yes', $no = 'adverb.no', $textDomain = 'app-common')`
                Can use a custom text, only need overwrite `$yes`, `$no` and `$textDomain` parameters
    -   New feature on functions:
        -   `navigation_pagination`
            -   Now compatible with Jaxon-PHP
                -   Change link url for Jaxon function. Example: `navigation_pagination(paginator, 'JaxonLotgd.Ajax.Core.Motd.list' )`
-  **Form system**
    -   _Laminas Form_:
        -   New Elements:
            -   ViewOnly
            -   PetitionType
    -   _Symfony Form_:
        -   New Types:
            -   BitFieldType
            -   ClanRankType
            -   CronjobListType
            -   DateTimeType
                -   Note: Only for add a transformer to avoid errors with invalid date `0000-00-00 00:00:00`
            -   RaceType
            -   SpecialtyType
-   **MOTD** now use Jaxon to load data. It is not embedded in the modal.
-   **Petition for Help** now use Jaxon to load data. It is not embedded in the modal.
-   **Game Mail** _Ye Olde Mail_ now use Jaxon to load data. It is not embedded in the modal.

### :fire: DEPRECATED

-   Nothing

### :wrench: FIXES

-   **public/donators.php** Now show corrects text and not key translator.
-   **lib/clan/applicant.php** Added missing translation for navs.
-   **public/create.php** Added missing translation for navs.
-   **lib/modules/objpref.php** `increment_module_objpref` Fixed error that change previous value.

### :x: REMOVES

-   File **lib/data/configuration_cronjob.php** Not is necesary.
-   File **lib/data/user_account.php** Not is necesary.
-   File **public/motd.php** Now use Jaxon to load MOTD.
-   File **public/petition.php** Now use Jaxon to load petition for help.
    -   File **lib/petition/petition_default.php** No need now.
-   File **public/mail.php** Now use Jaxon to load Ye Olde Mail.
-   **Translations** yaml files, removed used arrays as multiline text. Use folded style with `>` or `|`
    -   Can find examples in files translations
    -   More info of YAML format in https://symfony.com/doc/4.4/components/yaml/yaml_format.html#strings
-   **Remove obsolete functions**
    -   **lib/modules.php**
        -   `module_sem_acquire`
        -   `module_sem_release`
    -   **lib/datacache.php** delete file and all deprecated function.
        -   `datacache`
        -   `updatedatacache`
        -   `invalidatedatacache`
        -   `massinvalidate`
        -   `datacache_empty`
        -   `datacache_clearExpired`
        -   `datacache_optimize`

### :notebook: NOTES

-   **Migration: Zend to Laminas** migration all packages of Zend Framework to Laminas
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

# Version: 4.2.0

### :cyclone: CHANGES

-   :warning: **Doctrine** Now used DoctrineModule and DoctrineORMModule to configure Doctrine
    -   Check `config/autoload/global/doctrine-lotgd-core.php` to see possible configuration
    -   Need create configuration in `config/autoload/local/dbconnect.php` like this:
```php
    //-- DB conection
    //...
    return [
        'lotgd_core' => [
            //-- Zend DB Config
        ],
        'doctrine' => [
            'connection' => [
                'orm_default' => [
                    'params' => [
                        'driver' => 'pdo_mysql', //-- In lowercase
                        'user' => 'user name',
                        'password' => ' password',
                        'host' => '127.0.0.1', //-- localhost
                        'dbname' => 'data base name',
                        'charset' => 'utf8',
                        'collate' => 'utf8_unicode_ci'
                    ]
                ]
            ]
        ]
    ];
```
-   **lib/saveuser.php** Added option to avoid save laston when save user (used in jaxon request)
-   :warning: **Translation in entities** Some database tables that have fields that are susceptible to translation can now be translated using Symfony form.
    -   **src/core/Entity/Creatures.php**
        -   The following fields can now be translated
            -   `creaturecategory`
            -   `creaturename`
            -   `creaturedescription`
            -   `creatureweapon`
            -   `creaturelose`
            -   `creaturewin`
    -   **src/core/Entity/Masters.php**
        -   The following fields can now be translated
            -   `creaturename`
            -   `creatureweapon`
            -   `creaturelose`
            -   `creaturewin`
    -   **src/core/Entity/Companions.php**
        -   The following fields can now be translated
            -   `name`
            -   `category`
            -   `description`
            -   `jointext`
            -   `dyingtext`
    -   **src/core/Entity/Armors.php**
        -   The following fields can now be translated
            -   `name`
    -   **src/core/Entity/Weapons.php**
        -   The following fields can now be translated
            -   `name`
    -   **src/core/Entity/Mounts.php**
        -   The following fields can now be translated
            -   `mountname`
            -   `mountdesc`
            -   `mountcategory`
            -   `newday`
            -   `reacharge`
            -   `partrecharge`
    -   **src/core/Entity/Titles.php**
        -   The following fields can now be translated
            -   `male`
            -   `female`

-   **THEME**
    -   Updated Fomantic UI version: 2.8.3 => 2.8.6

### :star: FEATURES

-   **Jaxon-PHP** are now in a factory, so you can customize with a config file in `config/autoload/local/*`
    -   Can get it's factory with `\LotgdLocator::get(Lotgd\Core\Jaxon::class);`
-   **Form System** improved:
    -   Now can use Symfony Form and Zend Form (Laminas Form in 4.3.0 IDMarinas Edition)
    -   Lotgd Core use Symfony Forms only for Update/Created entities
    -   Zend Form use for configuration of game, and for free forms.
    -   Note: Zend forms may be removed in the future and only Symfony Forms used, but it is not planned for now.

### :fire: DEPRECATED

-   **Translations** *yaml files* Deprecated used arrays as multiline text. Can use folded style with `>` or `|`
    -   Can find examples in file translation
    -   In next version `4.3.0` all arrays are formated with this format `key1.key1.0`, `key1.key1.1`, `key1.key1.2`
    -   More info in https://symfony.com/doc/4.4/components/yaml/yaml_format.html#strings

### :wrench: FIXES

-   **lib/modules/modules/modulestatus.php** Fixed error, now when file exist continue with script.
-   **public/motd.php** Fixed error when there is no motd in the database
-   **public/graveyard.php** Fixed errors
    -   Now show navs when finish battle
    -   Hide menu of search when not have soulpoints/hitpoints
-   **src/core/EntityRepository/ArmorRepository.php** and **src/core/EntityRepository/WeaponsRepository.php**
    -   Fixed error with get max level of armor/weapon to show (Always get it max level) according to DragonKills of character.
        -   This is in shop of armor and weapons
-   **Fixed error in new installation**
    -   Now save correct data when data is an array.
        -   **src/core/Installer/data/install/companions.json**
        -   **src/core/Installer/data/install/mounts.json**
    -   Now show all modules that are uninstalled in clean install.
-   Fixed error when need set race and not are races installed.
-   **src/core/Entity/Characters.php** Fixed error, with data returned
-   **public/forest.php** Fixed: use correct text domain for navigation
-   **lib/newday/dragonpointspend.php**, **lib/newday/setrace.php**
    -   Fixed correct templates and now show correct info.
-   **public/dragon.php** Fixed error, now can progress when kill the Dragon.

### :x: REMOVES

-   Nothing

### :notebook: NOTES

-   :warning: **PHP** LoTGD Core now need min PHP version 7.2
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies
    -  Replace plugin  `uglifyjs-webpack-plugin` for `terser-webpack-plugin`

# Version: 4.1.0

### :cyclone: CHANGES

-   Moved some folders of `data/` to `storage/`
    -   `data/cache/*` => `storage/cache/*`
    -   `data/log/*` => `storage/log/*`
    -   `data/logd_snapshots/*` => `storage/logd_snapshots/*`
-   Commentary table new field:
    -   `comment_raw` Save raw comment without filters
-   Translation domain for some  pages:
    -   Armor editor: `page-armoreditor` to `grotto-armoreditor`
    -   Character backup: `page-characterbackup` to `grotto-characterbackup`
    -   Companions: `page-companions` to `grotto-companions`
    -   Configuration: `page-configuration` to `grotto-configuration`
    -   Creatures: `page-creatures` to `grotto-creatures`
    -   Debug: `page-debug` to `grotto-debug`
    -   Game log: `page-gamelog` to `grotto-gamelog`
    -   Global user functions: `globaluserfunctions` to `grotto-globaluserfunctions`
    -   Masters: `page-masters` to `grotto-masters`
    -   Moderate: `page-moderate` to `grotto-moderate`
    -   Modules: `page-modules` to `grotto-modules`
    -   Mounts: `page-mounts` to `grotto-mounts`
    -   Paylog: `page-paylog` to `grotto-paylog`
    -   Rawsql: `page-rawsql` to `grotto-rawsql`
    -   Referers: `page-referers` to `grotto-referers`
    -   Stats: `page-stats` to `grotto-stats`
    -   Superuser: `page-superuser` to `grotto-superuser`
    -   Title edit: `page-titleedit` to `grotto-titleedit`
    -   User: `page-user` to `grotto-user`
    -   View petition: `page-viewpetition` to `grotto-viewpetition`
    -   Weapon editor: `page-weaponeditor` to `grotto-weaponeditor`
    -   Note: all this pages is a Grotto section.
-   Change in core **cache** system:
    -   Now use Cache Abstract Factory (Zend-Cache) to load all caches (you can create your own caches)
    -   Delete Lotgd Cache class `Lotgd\Core\Lib\Cache` and `Lotgd\Core\Factory\Lib\Cache`
    -   Cache not is optional, always use when invoke functions of cache.
    -   Added a static class for cache `LotgdCache`
-   **public/bank.php** Use `{ownerName}` for name of Banker name
-   :warning: **_Changed Form system_**
    -   This new version brings a new way of displaying forms.
    -   The Zend\Form and Twig templates components are now used to display the forms.
    -   *The old system will continue to work for the time being. Until the migration of all forms to the new system is completed.*
    -   The new system uses the arrays to generate the forms, similar to the old form but using the Zend\Form structure.
    -   An example can be found in the basic game configuration form. It has the file with all the elements of the form and another one with the filters.
    -   The filters for all the inputs is a new feature. To know more you can see the Zend\Form documentation.
    -   Game use a zend (form/input-filter) abstract factory to load all forms and input-filters.
    -   You just need to follow the example in the following files. But remember to create a different file to add your forms and input-filter in the folder `config/autoload/local`
        -   `config/autoload/global/form-lotgd-core.php` This file contains the forms
        -   `config/autoload/global/input-filter-lotgd-core.php` In this file are saved the input-filters
-   **THEME**
    -   Updated Fomantic UI version: 2.7.8 => 2.8.3
    -   Updated SweetAlert 2 version: ^8.18.0 => ^9.*

### :star: FEATURES

-   **lib/configuration/cache.php** Added compatibility with multi-cache. Can optimize multiple caches.

### :fire: DEPRECATED

-   **lib/datacache.php** deprecated all functions. Use `LotgdCache` static class for cache functions
-   **lib/showform.php** deprecated all functions. Use new form system Zend Form or Symfony Form

### :wrench: FIXES

-   `lib/checkban.php` Fixed "error" now use Doctrine to access DB
-   `public/rawsql.php` Fixed error, now show results of SQL query.
-   Global user functions, fixed error with domain for translation `globaluserfunctions` => `grotto-globaluserfunctions`
-   `data/translation/en/partial/taunt.yaml` Fixed error with name of var in taunt number 25 and fixed error in count.
-   `data/translation/en/app/mail.yaml` Fixed error with name of var.

### :x: REMOVES

-   Removed obsoleted functions from files that used it, including the datacache functions.
-   Removed files:
    -   `lib/censor.php` use new censor system `$censor = \LotgdLocator::get(\Lotgd\Core\Output\Censor::class); $censor->filter(string)`
    -   `lib/commentary.php` use new commentary system
    -   `lib/http.php`
    -   `lib/nav.php`
    -   `lib/superusernav.php`
    -   `lib/tabledescriptor.php`
    -   `lib/villagenav.php`
    -   `lib/errorhandler.php` Now LoTGD have a new way to register errors and exceptions with Tracy Debugger
    -   `lib/show_backtrace.php`
    -   `lib/output_array.php` This file was not being used
    -   `lib/stripslashes_deep.php` This file was not being used
-   Removed vars from **common.php**: `$logd_version`, `$copyright` and `$license` use:
    -   Public display version: `Lotgd\Core\Application::VERSION`
    -   Identify numeric version: `Lotgd\Core\Application::VERSION_NUMBER`
    -   Copyright text: `Lotgd\Core\Application::COPYRIGHT`
    -   License text: `Lotgd\Core\Application::LICENSE`
-   Removed functions from:
    -   File: `lib/playerfunctions.php`
        -   `is_player_online()`
        -   `mass_is_player_online()`
        -   `get_player_dragonkillmod()`
        -   `get_player_info()`
-   Removed obsoleted functions from `src/core/Output/Collector.php`:
    -   `output()`
    -   `output_notl()`
    -   Note: Use new translations system and template system.
-   Removed Settings extended from core:
    -   Files/Classes `Lotgd\Entity\SettingsExtended` and `Lotgd\Core\Factory\Lib\SettingsExtended`
    -   Note: This configuration was not being used
-   Removed obsolete function `each()` PHP 7.2.0 from some files.
    -   `lib/translator.php` and `lib/showform.php` For now these files retain the function as they will be deleted in a future version
-   Twig template:
    -   Removed obsolete filter `sustitute`
    -   Removed obsolete filter `nltoappon` use Twig filter `nl2br`

### :notebook: NOTES

-   Optimization Many files `.php` have had a slight optimization of code using CS Fixer.
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

# Version: 4.0.0

### :cyclone: CHANGES

-   :warning: **_Changed Structure of files for web_**
    -   Most of the Web files have been reorganized.
        -   Some files/folders are moved to `public/` folder
            -   Primary files like: `about.php`, `accounts.php`, `armor.php`, `create.php`... (all .php files in root).
            -   Folder `images/`
            -   Folder `resources/` is renamed to `js/` and `ccs/`
            -   Folder `themes/`
        -   Moved folder `cache` to `data/cache`
        -   Moved folder `templates` to `data/template`
        -   Moved folder `crawl` to `data/cache/crawl` This folder is where the advertising system keeps a cached copy.
        -   Moved folder `logd_snapshots` to `data/logd_snapshots` This folder is where the copies of the deleted files are stored, so that they can be restored.
-   :warning: **_Changed Translation system_**
    -   Remplaced old system of translation for new system. About this new system:
        -   Used a custom _Translator_ based in `Zend\I18n\Translator\Translator`
        -   This new system not used Data Base to store translations.
        -   Used `.yaml` files to store translations.
            -   Files are in `data/translation/[LOCALE]/[SCOPE]/[DOMAIN].yaml` This is de main structure.
                -   By default have six scopes:
                    -   `app` This is where the translation files from main of game.
                    -   `module` This is where the translation files are stored in the modules.
                    -   `navigation` This is where the translation files from the navigation menu are stored.
                    -   `page` This is where the translation files from the main pages are stored.
                    -   `partial` This is where the translation files from partials (other text).
                    -   `popup` This is where the translation files from popup text.
            -   The translations are automatically loaded by the translation factory.
                -   It is possible to have more scopes besides `page`, `module` ... but remember the structure of the folder `translation`.
            -   In addition, scopes can be nested to better organize translation files. Example:
                -   You can use this text domain in your module: `module-village-navigation`, which would be equivalent to the file `data/translation/[LOCALE]/module/village/navigation.yaml`
                    -   `[LOCALE]` is the code of the language to be translated, according to the default language and the language chosen by the user.
-   :warning: **_Changed navigation menu system_**
    -   The old `addnav` and other similar functions are remplaced with new Navigation menu system.
        -   You can add/block new navs with this functions:
            -   `LotgdNavigation::addHeader(string $header, array $options = [])`
            -   `LotgdNavigation::addHeaderNotl(string $header, array $options = [])`
            -   `LotgdNavigation::addNav(?string $label, ?string $link = null, array $options = [])`
            -   `LotgdNavigation::addNavNotl(?string $label, ?string $link = null, array $options = [])`
            -   `LotgdNavigation::addNavAllow(string $link)` This not add new nav, only allow link.
            -   `LotgdNavigation::blockLink(string $link)`
        -   With this function you can change the translation domain for the affected menus (Like `tlschema($schema = false)`)
            -   `LotgdNavigation::setTextDomain(?string $domain = null)`
            -   If you use `LotgdNavigation::setTextDomain(null)` or `LotgdNavigation::setTextDomain()` reset the translation domain to the previous value.
    -   _Note:_ New system not allow html tags in navs name. All labels of navs are filtered to strip this.
-   :warning: **_Changed commentary system_**
    -   The old comment system has been replaced by a new one.
    -   The new system have new structure of table in data base.
    -   All old comments with are imported to new system. (May be failed)
        -   Before upgrade to 4.0.0 version, make a optimimization and clean your data base. Deleting very old comments.
    -   For show and add comments, you need this Twig functions: (Example in _village.twig_)
        -   `{{ commentary_block(commentary, textDomain) }}` Show comment block
            -   `commentary` Is an array with options for commentary:
                -   Array example of village:
                ```
                    [
                        'section' => 'village', //-- This is name of section for comentaries
                        'textDomainStatus' => 'app-commentary' //-- This is optional, and only is necesary if you want change de domain for translate text of player status. Default is texDomain defined in _commentary_block_
                    ]
                ```
            -   `textDomain` Is the text domain for translator. In case of village is `page-village`.
        -   This are optional, use if need change default values:
            -   `{% commentary_limit_comments 10 %}`, Set a limits of comments per page. Default is 25
            -   `{% commentary_show_pagination false %}`, Set if show pagination of comments. Default is true
            -   `{% commentary_pagination_link_url 'village.php' %}`, Set the url for links of pagination. Default is `$_SERVER['REQUEST_URI']`
            -   `{% commentary_can_add_comments false %}`, Set if can add new comments. Default is true
-   :warning: **_Changed death message and taunts_**
    -   Removed `public/deathmessages.php` and `public/taunts.php`. Can't edit this in data base. Now use translation system.
    -   Can found all death and taunts messages in `translation/en/partial/deathmessage.yaml` and `translation/en/partial/taunt.yaml`
    -   **DeathMessages**
        -   Can separate messages by zones and can add more messages for each zone.
            -   All zones (except default) have a key `count` with count of messages for zone.
            -   All languages must have the same number of messages in each zone.
                -   This is because when the game selects a random message, it may not exist in another language, and the translation fails.
        -   Default zone have only 1 message.
    -   **Taunts**
        -   Have a `count` key with a count of all taunts messages.
        -   All languages must have the same number of taunts messages
            -   This is because when the game selects a random message, it may not exist in another language, and the translation fails.
-   **lib/jaxon.php** Now use Semantic Modal instead of the default modal.
-   **public/source.php** Source page not show source of file. No need when all code are public in repository in Github.
-   **lib/pageparts.php** Alter `page_header` and `popup_header` function. New format to add title to page.
    -   `page_header(?string $title = null, array $params = [], ?string $textDomain = null)`
    -   `popup_header(?string $title = null, array $params = [], ?string $textDomain = null)`
    -   _Notes_: You need to change all `page_header` and `popup_header` functions that content an array as the first argument. The first argument must be only a string or null.
-   **common.php** Updated script of log http referers.
    -   Changes the way that static classes are used:
        -   **lib/class/dbwrapper.php** now are in **src/core/Fixed/Dbwrapper.php**
        -   **lib/class/doctrine.php** now are in **src/core/Fixed/Doctrine.php**
        -   **lib/class/lotgdFormat.php** now are in **src/core/Fixed/Format.php**
        -   **lib/class/servicemanager.php** now are in **src/core/Fixed/Locator.php**
        -   **lib/class/template.php** now are in **src/core/Fixed/Theme.php**
-   **src/core/Lib/Settings.php** It improves the management of the settings cache.
-   **src/core/Factory/Lib/Doctrine.php** Proxy and cache of Doctrine are located in cache dir of game
-   **src/core/Output/Collector.php** Method `appopencode` changed and improved.
    -   Some files have been modified to fit this
    -   _Updated_ For keywords of `sustitute()` function:
        -   _Note_: This function is not recommended. Because you can use the translator to do the same.
        -   `{playername}` Replaced by player's name (**Without** the title included)
        -   `{charactername}` Replaced by player's name (**With** the title included)
        -   `{playerweapon}` Replaced by the name of the player's weapon
        -   `{playerarmor}` Replaced by the name of the player's armor
    -   Now for close color/code code you can use:
        -   Use it's own code with **´** before. Examples:
            -   `` `4This text is dark red´4``
            -   `` `@This text is green´@``
            -   `` `cThis is a center text´c``
            -   `` `iThis is cursive text´i``
            -   `` `bThis is strong text´b``
            -   ``This text have a line break`n`` This code not have a close format
        -   Use `` `0`` this method work with all colors (only)
            -   `` `4This text is dark red`0``
            -   `` `@This text is green`0``
        -   The system does not autoclose the codes, so you need to close all the codes (when necessary), otherwise the result may vary.
-   **src/core/Http.php** The original function is altered to change the value of some keys.
-   **DataBase** Table `accounts` are divided, now information of character are in table `characters`
-   **New namespaces for this**
    -   `Lotgd\Core\Lib\Dbwrapper` changed to `Lotgd\Core\Db\Dbwrapper`
    -   `Lotgd\Core\Lib\Doctrine` changed to `Lotgd\Core\Db\Doctrine`
-   **Twig template**
    -   The translation filter is added as an extension
        -   The new translation filter allows you to set a target language per template
            -   Syntax: `{% translate_default_domain 'scope-domain' %}`
                -   `scope` is "page" or "module". You can add more. Is a folder where is domain file.
                -   `domain` is a name of `.yaml` file. (Avoid extension)
-   **THEME**
    -   Semantic UI `2.4.2` is remplace with Fomantic UI `2.7.4`
        -   [Fomantic UI](https://github.com/fomantic/Fomantic-UI) is a fork of [Semantic UI](https://github.com/Semantic-Org/Semantic-UI).
        -   Why? Because Semantic UI have a low activity, community forked the project with intention in merge with Semantic UI when project back to active again.
        -   Nothing changed with this.
    -   :warning: **_New structure for theme templates and changed how created new themes_**
        -   All theme templates are moved to `data/template/` folder
        -   File `jade.html` moved too.
        -   To create new themes now you can extend original theme, and them customize templates.

### :star: FEATURES

-   _**New Installer system**_ This version have a new installer of game.
    -   :warning: The new installation system only allows upgrading from the previous version.
    -   You can no longer choose whether to use the cache and the cache directory during installation.
        -   To change this you need to do it from a local configuration file in the directory "config/autoload/local/cache.php"
            > An example of how to configure the cache can be found in the file "config/lotgd.config.php".
    -   Now check if you can write to the cache directory.
-   _**New maintenance mode**_
    -   Core game now have a way to active a maintenance mode.
    -   It has two modes:
        -   One is a warning to users that they have to disconnect
        -   Other mode forces the disconnection of anyone who does not have developer permission.
    -   In both modes it only allows those with developer permission to connect to the game.
    -   No problem if both modes are active.
    -   Are in standar configuration.
-   _**New Component for Game**_ `Lotgd\Core\Component\Filesystem`
    -   This component extend component of `Symfony\Component\Filesystem\Filesystem` and add a new method:
        -   `$filesystem->listDir(string $dir)` List files in directory (not recursive)
-   _**New profanity filtering for comments and other**_
    -   Now you avoid saving the data in the database and use PHP files, in the folder `data/dictionary/{LANGUAGE_CODE}.php`.
-   _**New Account Backup**_ When delete an account now game generate a backup for this account.
    -   Data are saved in `data/logd_snapshots/account-[account_id]/`
    -   Data saved by default are:
        -   All information of _account_
        -   Information of _character_
        -   All _mails_ to account
        -   All _news_ of account
        -   All _comments_ of account
        -   All _module_userprefs_ of account
    -   Can see Backups in Grotto -> Mechanics -> Character Backup
        -   Here can:
            -   View the list of backups
            -   View backup detail
            -   Restore a backup
            -   Delete a backup
    -   In order to create a backup and delete the data, is necesary that the EntityRepository of each table needs to have the following two methods:
        -   `public function backupDeleteDataFromAccount(int $accountId): array {}`
        -   `public function backupGetDataFromAccount(int $accountId): int {}`
    -   Can use hook `character-cleanup` to add new content to backup
        ```
        modulehook('character-cleanup', [
                'entities' => [
                    //-- Delete data from DataBase of all entities here
                    // 'Entity:Name' => Backup: true|false,
                    'LotgdCore:Mail' => true,
                    'LotgdCore:News' => true,
                    'LotgdCore:AccountsOutput' => false, //-- The data is not backed up, but it is deleted.
                    'LotgdCore:Commentary' => true,
                    'LotgdCore:ModuleUserprefs' => true
                ],
                'acctid' => $accountId,
                'deltype' => $type
            ])
        ```
        > Can use short name of Entity `LotgdCore:Mail` or full name `Lotgd\Core\Entity\Mail`
    -   Can use hook `character-restore` to customize how restore data of entity
        ```
        modulehook('character-restore', [
                'entity' => $file['shortNameEntity'],
                'proccessed' => false,
            ])
        ```
        > `$file['shortNameEntity']` is the name you use in hook `character-cleanup`
-   :warning: **_LotGD use Twig template system_**
    -   It's a system similar to MVC.
    -   This means that all LotGD pages use the Twig template system to show all the text, no more `output()` or `output_notl()` functions are used to show text.
    -   All pages have a hook called `page-[NAMEPAGE]-tpl-params` with this hook you can change/add new parameters for the templates you can use in your theme.
        -   Example: `modulehook('page-home-tpl-params', [array $params])`
            -   _Note_: This _modulehook_ is executed just before displaying the template.
        -   Some pages can have this structure for hook `page-[NAMEPAGE]-[SUBNAME]-tpl-params` are pages that have a params in route. Like page "about".
            -   Example: `modulehook('page-about-license-tpl-params', [array $params])`
-   :city_sunset: _**New ambience system**_ This version have a new way for create ambience in villages and other zones.
    -   Before, the best way to create an ambience (I take the village as an example) was with a hook that received all the texts and could be customized.
    -   Now you can, with the new translation system, it is simpler, since you only have to change the `textDomain` and/or `textDomainNavigation` for the page.
        -   New hook that allows to change the domain of the text in certain zones to create ambience.
            -   This is the hook of village `modulehook('village-text-domain', ['textDomain' => 'page-village', 'textDomainNavigation' => 'navigation-village'])`
                -   With this you can change text in all village and navigation, for create ambience.
                -   You just need to copy the corresponding files and do the translation/adaptation you need.
                -   This files are in `data/translation/en/page/village.yaml` and `data/translation/en/navigation/village.yaml`
                -   _Example of custom translation files for the village_: `data/translation/en/page/village_elf.yaml` and `data/translation/en/navigation/village_elf.yaml`
                    -   Hook return: `['textDomain' => 'page-village_elf', 'textDomainNavigation' => 'navigation-village_elf']`
    -   Zones that have this new hook are:
        -   `public/armor.php` hook is `modulehook('armor-text-domain', ['textDomain' => 'page-armor', 'textDomainNavigation' => 'navigation-armor'])`
        -   `public/bank.php` hook is `modulehook('bank-text-domain', ['textDomain' => 'page-bank', 'textDomainNavigation' => 'navigation-bank'])`
        -   `public/dragon.php` hook is `modulehook('dragon-text-domain', ['textDomain' => 'page-dragon', 'textDomainNavigation' => 'navigation-app'])`
        -   `public/forest.php` hook is `modulehook('forest-text-domain', ['textDomain' => 'page-forest', 'textDomainNavigation' => 'navigation-forest'])`
        -   `public/gardens.php` hook is `modulehook('gardens-text-domain', ['textDomain' => 'page-gardens', 'textDomainNavigation' => 'navigation-gardens'])`
        -   `public/graveyard.php` hook is `modulehook('graveyard-text-domain', ['textDomain' => 'page-graveyard', 'textDomainNavigation' => 'navigation-graveyard'])`
        -   `public/gypsy.php` hook is `modulehook('gypsy-text-domain', ['textDomain' => 'page-gypsy', 'textDomainNavigation' => 'navigation-gypsy'])`
        -   `public/healer.php` hook is `modulehook('healer-text-domain', ['textDomain' => 'page-healer', 'textDomainNavigation' => 'navigation-healer'])`
        -   `public/hof.php` hook is `modulehook('hof-text-domain', ['textDomain' => 'page-hof', 'textDomainNavigation' => 'navigation-hof'])`
        -   `public/inn.php` hook is `modulehook('inn-text-domain', ['textDomain' => 'page-inn', 'textDomainNavigation' => 'navigation-inn'])`
        -   `public/lodge.php` hook is `modulehook('lodge-text-domain', ['textDomain' => 'page-lodge', 'textDomainNavigation' => 'navigation-lodge'])`
        -   `public/mercenarycamp.php` hook is `modulehook('mercenarycamp-text-domain', ['textDomain' => 'page-mercenarycamp', 'textDomainNavigation' => 'navigation-mercenarycamp'])`
        -   `public/newday.php` hook is `modulehook('newday-text-domain', ['textDomain' => 'page-newday', 'textDomainNavigation' => 'navigation-newday'])`
        -   `public/rock.php` hook is `modulehook('rock-text-domain', ['textDomain' => 'page-rock', 'textDomainNavigation' => 'navigation-rock'])`
        -   `public/shades.php` hook is `modulehook('shades-text-domain', ['textDomain' => 'page-shades', 'textDomainNavigation' => 'navigation-shades'])`
        -   `public/stables.php` hook is `modulehook('stables-text-domain', ['textDomain' => 'page-stables', 'textDomainNavigation' => 'navigation-stables'])`
        -   `public/train.php` hook is `modulehook('train-text-domain', ['textDomain' => 'page-train', 'textDomainNavigation' => 'navigation-train'])`
        -   `public/village.php` hook is `modulehook('village-text-domain', ['textDomain' => 'page-village', 'textDomainNavigation' => 'navigation-village'])`
        -   `public/weapon.php` hook is `modulehook('weapon-text-domain', ['textDomain' => 'page-weapon', 'textDomainNavigation' => 'navigation-weapon'])`
-   _**New CronJob**_ This CronJob searches all long inactive accounts and logout them.
-   **src/core/Template/Theme.php** and **src/core/Fixed/Theme.php**
    -   Added new function `renderModuleTemplate(string $template, array $params)` With this function you can render a template of a module that does not depend on the current theme.
-   **lib/class/lotgdFormat.php** Added new function:
    -   `LotgdFormat::pluralize(int $number, string $singular, string $plural)` select the plural or singular form according to the past number
-   **_Migrating to Doctrine_**
    -   In this version game are migrating to Doctrine ORM for access to data base.
    -   You can still use Zend DB and its classes to access it, but it is recommended to use Doctrine, as this maintains the integrity of the database.
        -   As an example, some fields in the tables are of type serialized array, Doctrine serializes and deserializes these fields automatically.
    -   There are no plans to remove Zend DB at this time.
-   **Debugger** Added a debugger (Tracy library) to LotGD
    -   Can use `Debbugger::log($string)` to log a string.
        -   Can use to log a `Throwable` instance too.
    -   You can dump a var with this options:
        -   `Debugger::barDump($var, string $title)` or `barDump($var, string $title)` dump var to Debugger bar. To this option you can add a title to the dump
        -   `Debugger::dump($var)` or `dump($var)` dump var in output.
    -   With  can dump a var, and in production Debugger ignore this.
    -   Doctrine: can see the number and SQL queries in Tracy debugger bar.
-   **Since 4.0.0 IDMarinas Edition** When a module is installed, the game checks if the module requires a specific version of LoTGD.

### :fire: DEPRECATED

-   **common.php** Var: `$logd_version`, `$copyright` and `$license` are now DEPRECATED:
    -   Public display version: `Lotgd\Core\Application::VERSION`
    -   Identify numeric version: `Lotgd\Core\Application::VERSION_NUMBER`
    -   Copyright text: `Lotgd\Core\Application::COPYRIGHT`
    -   License text: `Lotgd\Core\Application::LICENSE`
    -   Functions:
        -   `output()`
        -   `output_notl()`
-   **lib/nav.php** All functions:
    -   `blocknav()`
    -   `unblocknav()`
    -   `appendcount()`
    -   `appendlink()`
    -   `set_block_new_navs()`
    -   `addnavheader()`
    -   `addnav_notl()`
    -   `addnav()`
    -   `is_blocked()`
    -   `count_viable_navs()`
    -   `checknavs()`
    -   `buildnavs()`
    -   `private_addnav()`
    -   `navcount()`
    -   `clearnav()`
    -   `clearoutput()`
    -   `add_accesskey()`
    -   _Note_: This file will be deleted in version 4.1.0
-   **settings_extension** Some of these settings (soon all) have been transferred to the translation file `data/translations/en/app/mail.yaml` They don't need to be in the database.
-   **lib/villagenav.php**
    -   `villagenav()` use `LotgdNavigation::villageNav()`
    -   _Note_: This file will be deleted in version 4.1.0
-   **lib/superusernav.php**
    -   `superusernav()` use `LotgdNavigation::superuserGrottoNav()`
    -   _Note_: This file will be deleted in version 4.1.0
-   **lib/http.php**:
    -   `httpget()` use `LotgdHttp::getQuery()`
    -   `httpallget()` use `LotgdHttp::getAllQuery()`
    -   `httpset()` use `LotgdHttp::setQuery()`
    -   `httppost()` use `LotgdHttp::getPost()`
    -   `httppostisset()` use `LotgdHttp::existInPost()`
    -   `httppostset()` use `LotgdHttp::setPost()`
    -   `httpallpost()` use `LotgdHttp::getPostAll()`
    -   `postparse()`
-   **lib/censor.php**:
    -   `soap()`
    -   `good_word_list()`
    -   `nasty_word_list()`
-   **lib/commentary**: All functions, use new commentary system.
    -   `commentarylocs()`
    -   `removecommentary()`
    -   `restorecommentary()`
    -   `commentcleanup()`
    -   `addcommentary()`
    -   `injectcommentary()`
    -   `injectsystemcomment()`
    -   `injectrawcomment()`
    -   `commentdisplay()`
    -   `viewcommentary()`
    -   `preparecommentaryblock()`
    -   `getcommentary()`
    -   `preparecommentaryline()`
    -   `commentaryfooter()`
    -   `buildcommentarylink()`
    -   `talkform()`
-   **lib/playerfunctions.php**
    -   `is_player_online()`
    -   `mass_is_player_online()`
    -   `get_player_dragonkillmod()`
    -   `get_player_info()`
-   **lib/tabledescriptor.php**: All functions, use Doctrine Entities to sync table schema.
    -   `synctable()`
    -   `table_create_from_descriptor()`
    -   `table_create_descriptor()`
    -   `descriptor_createsql()`
    -   `descriptor_sanitize_type()`
-   **lib/translator.php**: All functions, use new translation system.
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
-   **lib/sanitize.php**: All functions.
    -   `sanitize($in)` use `LotgdSanitize::fullSanitize($string)`
    -   `newline_sanitize($in)` use `LotgdSanitize::newLineSanitize($string)`
    -   `color_sanitize($in)` use `LotgdSanitize::fullSanitize($string)`
    -   `comment_sanitize($in)` New commentary system sanitize comments by default
    -   `logdnet_sanitize($in)` use `LotgdSanitize::logdnetSanitize($string)`
    -   `full_sanitize($in)` use `LotgdSanitize::fullSanitize($string)`
    -   `cmd_sanitize($in)` use `LotgdSanitize::cmdSanitize($string)`
    -   `comscroll_sanitize($in)`
    -   `prevent_colors($in)` use `LotgdSanitize::preventLotgdCodes($string)`
    -   `translator_uri($in)`
    -   `translator_page($in)`
    -   `modulename_sanitize($in)` use `LotgdSanitize::moduleNameSanitize($string)`
    -   `stripslashes_array($in)`
    -   `sanitize_name($spaceallowed, $inname)` use `LotgdSanitize::nameSanitize($spaceallowed, $inname)`
    -   `sanitize_colorname($spaceallowed, $inname, $admin = false)` use `LotgdSanitize::colorNameSanitize($spaceallowed, $inname, $admin = false)`
    -   `sanitize_html($in)` use `LotgdSanitize::htmlSanitize($string)`
    -   `sanitize_mb($in)` use `LotgdSanitize::mbSanitize($string)`
-   **src/core/Output/Collector.php**: Use new translations system and template system.
    -   `output()`
    -   `output_notl()`
-   **Twig Template System**
    -   Filters
        -   `sustitute` use new template system to simulate this.
        -   `nltoappon` use Twig filter `nl2br`

### :bug: FIXES

-   **Folder name** `Lotgd\Core\Patern\Container` to `Lotgd\Core\Pattern\Container` I found a error in name of folder :laughing:
-   **lib/class/dbwrapper.php**
    -   Fixed error, not passed param $prefixed
    -   Only one instance of `Zend\Paginator\Paginator` can be passed
-   **lib/configuration/configuration_data.php** Fixed error with data
-   **lib/data/configuration_data.php** Deleted unused data
-   **lib/nav.php** Fixed error with new color/code syntax
-   Check if have connect before execute function
    -   **src/core/Lib/Dbwrapper.php**
    -   **src/core/Lib/Settings.php**
    -   **src/core/Lib/Pattern/Zend.php**
-   **common.php** Fixed error with clean installation
-   **src/core/Factory/Character/Stats.php** Deleted var not defined (and unused)
-   **installer.php** Upgrade min version of PHP, since 2.7.0 IDMarinas Edition, min PHP version is 7.0, but installer check that min PHP version is 5.6

### :x: REMOVES

-   **common.php** Code removed for upgrade from version 2.7.0 to 3.0.0 IDMarinas edition
-   **public/badword.php** Remplaced for new censor _BanBuilder_
-   **lib/dbwrapper.php** Removed deprecated method `query_cached`
    -   Delete method `get_server_version` this is a special info, can use factory `Lotgd\Core\Lib\Dbwrapper` to get this info
-   **lib/pageparts.php** Removed deprecated function `popup`
-   **lib/php_generic_environment.php** and **lib/register_global.php** Removed from core.
    -   Not is necesary register as global all data in `$_SERVER`. Can use `LotgdHttp::getServer(string $name = null , string $default = null)`
-   **lib/clan/func.php** Deleted from the core, it wasn't being used.
-   **newday.php** Deleted support for Dragon Points for legacy options::
    -   Max Hitpoints + 5
    -   Attack + 1
    -   Defense + 1

### :notebook: NOTES

-   :warning: _Important_ This is a very large update, which is going to require a lot of changes.
    -   All the old translation functions are present, but they may not work as expected. These functions issue an obsolete function warning message.
    -   All pages are changed to use new Translation and Template system.
    -   **TIP:** Before upgrading to version 4.0.0, deactivate all modules, then on the installation screen mark modules to install modules that are upgraded for this version.
-   **DB::** Now Lotgd Core use Doctrine to access DB. 98% (or so) of code that use this class, now use Doctrine.
-   **Optimization** Some files are optimized for maintainability using sugestions of _Code Climate_
-   **Gulp** GulpJs is updated from version `3.9.1` to `4.0.0`
    -   All related gulp tasks are updated to this new version
    -   Removed `gulp-help` dependency (use `gulp --tasks` to list tasks)
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

# Version: 3.0.0

### :cyclone: CHANGES

-   **Improve** performance and prevent security vulnerabilities for external links in this files:
    -   **common.php**
    -   **lotgdnet.php**
    -   **source.php**
    -   **lib/nav.php**
    -   **lib/about/about_default.php**
    -   **lib/about/about_license.php**
    -   **lib/configuration/configuration_cronjob.php**
    -   **lib/installer/installer_sqlstatements.php**
    -   **lib/installer/installer_stage_1.php**
    -   **lib/installer/installer_stage_3.php**
    -   **templates/paypal.twig**
-   **translatetool.php** It uses the new way of generating queries to the DB (this method avoids problems with some characters when making queries, like for example the simple quotation)
-   **common.php** Now use Service Manager to load some factories. This method allows to use any factory and always load the same.
    -   Factories can be remplaced for your own
    -   `ob_start()` not use anymore `ob_gzhandler`
-   **lib/pageparts.php** Updating the JavaScript function name to the new name (Jaxon)
    -   Stat "Spirits" and "Gold" always show (not only when alive)
    -   Spirits "DEAD" only translate when show
-   _Using Service Manager_ in this files

    -   **lib/nav.php** Blockednavs are in Service Manager
    -   **lib/http.php**
    -   **lib/datacache.php**
    -   **lib/output.php** For generate class that use the output functions
    -   **lib/redirect.php** For load Output Collector

-   **lib/e_rand.php** Some improvements and added comments to functions
-   **lib/creaturefunctions.php** Now param `$packofmonsters` have a default value
-   **lib/modules.php** This file has been divided into different files, to improve comprehension.
    -   New files:
        -   **lib/modules.php**
        -   **lib/modules/actions.php**
        -   **lib/modules/blockunblock.php**
        -   **lib/modules/event.php**
        -   **lib/modules/hook.php**
        -   **lib/modules/injectmodule.php**
        -   **lib/modules/modulestatus.php**
        -   **lib/modules/objpref.php**
            -   Now used forced cache to get ObjPref for a given module. This data is a object config and not change so much.
        -   **lib/modules/prefs.php**
            -   In this part not used cache because with some modules not work good.
        -   **lib/modules/settings.php**
-   :warning: **_IMPORTANT_**
    -   **Jaxon** Files for jaxon has moved to new dir
    -   Can add your own files for jaxon in `src/ajax/local`
    -   **lib/class/template.php** Is now a class of static functions no needed be instantiated `LotgdTheme::`
        -   Using class `LotgdTheme::` for render templates:
        -   **armor.php**
        -   **clan.php**
        -   **create.php**
        -   **home.php**
        -   **weapons.php**
        -   **lib/lotgd_mail.php**
        -   **lib/nav.php**
        -   **lib/pageparts.php**
        -   **lib/about/about_listmodules.php**
        -   **lib/battle/functions.php**
        -   **lib/configuration/configuration_cache.php**
    -   **lib/class/lotgdFormat.php** Is now a class of static functions no needed be instantiated
        -   Using class `LotgdFormat::` for format numbers and any dates in:
            -   **bank.php**
            -   **donators.php**
            -   **list.php**
            -   **stats.php**
            -   **lib/commentary.php**
            -   **lib/template.class.php**
            -   **lib/bans/case\_.php**
            -   **lib/bans/case_removeban.php**
            -   **lib/bans/case_searchban.php**
            -   **lib/user/user\_.php.php**
            -   **lib/user/user_removeban.php.php**
            -   **lib/user/user_searchban.php.php**
-   **lib/installer/installer_stage_6.php** File `dbconnect.php` are in a new folder and have a new structure.
-   **THEME**
    -   Updated Semantic UI version 2.4.0 => 2.4.2

### :star: FEATURES

-   Now Lotgd IDMarinas Edition supported prefix for tables. You can add a prefix to name of tables in database. But may be not are full supported for any query in game. Remember use function `DB::prefix(string)` for add a prefix to name of table.
-   **lib/pageparts.php** Transfer character stats to a factory
-   **lib/class/doctrine.php** Add a Doctrine ORM to core of Lotgd. Now you can use in your modules.
    -   This, is perhaps, the prelude to its use in the whole core. :laughing:
    -   Functions allowed for now:
        -   `Doctrine::getRepository(string [EntityClassName])`
        -   `Doctrine::syncEntity(string [EntityClassName])` Synchronize a Entity with database.
        -   `Doctrine::syncEntities(array [EntityClassName])` Synchronizes an array of Entities with database.

### :fire: DEPRECATED

-   **lib/class/dbwrapper.php** Function `query_cached` is deprecated and deleted in a future version
    -   Use data cache system to cache data of query when needed
-   **lib/pageparts.php** Function `popup()` is deprecated and deleted in 3.1.0 version

### :wrench: FIXES

-   **bank.php** Fixed error by which you could not borrow money
-   **rumodule.php** Fixed error with link added with addnav
-   **lib/buffs.php** Fixed error with undefined index

### :x: REMOVES

-   **common.php** Code removed for version upgrade previous versions 1.0.0 IDMarinas edition and below
    -   This makes that from the 3.0.0 version it is impossible to update a previous version to the 1.0.0 IDmarinas Edition
-   **settings.php** Removed unused file
-   **lib/phpmailer/** Deleted all files, not used in Lotgd Core. If you need, you can load via Composer
-   **lib/sendmail.php** Removed from the core of the game, was not being used.
-   _Removed deprecate functions_
    -   **lib/datetime.php**
    -   **lib/class/dbwrapper.php**
    -   **lib/http.php**
    -   **lib/forestoutcomes.php**
    -   **lib/showform.php**
    -   **lib/template.class.php**
    -   **lib/template.php**

### :notebook: NOTES

-   **Optimization** Many files `.php`' have had a slight optimization of code using CS Fixer.

# Version: 2.7.0

### CHANGES

-   **lib/jaxon/class/timeout.class** Add options to prevent from Auto Hiding
-   **lib/lotgdFormat.php** `relativedate($indate)` now translate text before return
-   **lib/nav.php** Created function `add_accesskey()` to generate a keys for navs
-   **lib/http.php** Now use a Class `Zend\Http\PhpEnvironment\Request` store in var `$lotgd_request`. More info in: <https://docs.zendframework.com/zend-http/>
-   **viewpetition.php** Changed `each` loop for `foreach` loop. `each` are DEPRECATED IN PHP 7.2
-   **healer.php** Now with hook `healmultiply` you can change the multiplier and the cost of the healer's services.
-   **IMPORTANT**
    -   _Creatures system_
        -   Creatures in data base now not have:
            -   Level
            -   Health
            -   Gold
            -   Experience
            -   Attack
            -   Defense
        -   The creatures are generated in a dynamic way.
        -   Add option `creaturegoldbonus` to modify the amount of gold the creature is carrying
        -   Add option `creaturedefensebonus` to modify the defense of the creature has
        -   Add option `creatureattackbonus` to modify the attack of the creature has
        -   Add option `creaturehealthbonus` to modify the health of the creature has
        -   You can still alter a creature using the hook `buffbadguy`
        -   Example of usage are in Forest and Graveyard
-   **lib/graveyard/case_battle_search.php** and **forest.php** It adapts to generate the creature in a dynamic way.
-   **lib/creaturesfunctions.php** It creates function to look for creatures in the database and generate it dynamically.
-   **lib/forestcomes.php** Show a debug of creature when use `buffbadguy`
-   **_IMPORTANT_** Updated Twig to version 2.5. This version is only compatible with PHP >= 7.0
-   **JavaScript**
    -   Update Sweetalert2 from version 6.11.5 to 7.26.29
        -   Files in `assets` folder are updated
-   **THEME**
    -   Updated Semantic UI version 2.3.3 => 2.4.0
    -   Now all `.overrides` files of Jade Theme import default `.overrides` file (When need)
    -   Some adjustments are made to improve the appearance

### FEATURES

-   **lib/output.php** New system for replacing keywords for their value, using functions `output()` and `output_notl()`
    -   For now can use this keywords:
        -   `{playername}` Replaced by player's name
        -   `{playerweapon}` Replaced by the name of the player's weapon
        -   `{playerarmor}` Replaced by the name of the player's armor
-   **Templates**
    -   New filter `sustitute` Does the same as the `output()` and `output_notl()` functions
    -   Now you can access in templates with:
        -   `renderThemeTemplate()` and `renderTheme()`
            -   Var `userPre` that have array data of user, in `page_header()` or `popup_header()`
        -   Load templates with `renderThemeTemplate()` function:
            -   Var `session` have array data of session (exclude user data) in `page_footer()` or `popup_footer()`
            -   Var `user` that have array data of user, at the time the `renderThemeTemplate()` function is used
        -   Only available in `renderTheme()`
            -   Var `userPost` that have array data of user, in `page_footer()` or `popup_footer()`
-   **Javascript**
    -   Modal, added option for add classes to content

### DEPRECATED

-   Nothing

### REMOVES

-   **PHP** &lt;= 5.6 is not supported now by LOTGD IDMarinas Edition

### FIXES

-   **Installer script** Fixed error where it was not possible to install in a clean installation
-   **bio.php** It's about fixing the bug where you didn't create the URL correctly
-   **battle.php** Fixed error that didn't show the name of the creature
-   **forest.php** Fixed error that doppelganger was not generated correctly
-   **clan.php** Fixed error with undefined variable
    -   Fixed error in template **page/clan/new.twig**
-   **lib/serverfunctions.class.php** Fixed Error where in some cases the variable `$onlinecount` was not obtained
-   **lib/dbwrapper.php**
    -   The database connection check is improved.
    -   In SQL functions the query is omitted if a active connection to the database is not successful
    -   Fixed error of declaration, now use `Throwable` that is de correct declaration in PHP 7.0
-   **lib/commentary.php** Fixed possible error with undefined index
-   **lib/battle/buffs.php** Fixed error with incorrect use of functions
-   **lib/battle/extended.php** Fixed undefined index error
-   **THEME**
    -   **jade/template/battle/combathealthbar.twig** Fixed error for that show a string "array" when name is array (Battle in Graveyard)
-   Replaced obsolete function
    -   **lib/template.class.php**
    -   **lib/bans/case\_.php**
    -   **lib/bans/case_removebnan.php**
    -   **lib/bans/case_searchban.php**
    -   **lib/user/user\_.php**
    -   **lib/user/user_removeban.php**
    -   **lib/user/user_searchban.php**

### NOTES

-   **IMPORTANT** PHP &lt;= 5.6 is not supported now by LOTGD IDMarinas Edition
-   **Transfer repository** Transfer repository from Bitbucket to GitHub
-   **composer.json** Updated/Deleted dependencies
-   **package.json** Updated/Deleted dependencies
-   **gulp** Some gulp tasks have been updated
-   **Optimization** Most of `.php` files have had a slight code optimization using CS Fixer.

# Version: 2.6.0

### CHANGES

-   **battle.php** `$options` now have a new index `endbattle` that indicate if battle end
-   **motd.php**, **mail.php** and **petition.php** now open its content in a modal, and not in new window.
-   **Xajax** Xajax is replaced by Jaxon-PHP a fork of Xajax
-   _Templates_
    -   Any templates, added filters to colorize and translate text
    -   **battle.php** Template files in:
        -   Note: these files were moved from their previous folder `~/content`
        -   `~/pages/battle.twig`
            -   Now show image and description of creature if have it
        -   `~/pages/battle/combathealthbar.twig`
    -   **home.php** Template files in:
        -   Note: these files were moved from their previous folder `~/content`
        -   `~/pages/home/login.twig`
        -   `~/pages/home/loginfull.twig`
-   THEME
    -   Updated Semantic UI version 2.2.14 => 2.3.3
    -   Some adjustments are made to improve the appearance

### FEATURES

-   **lib/lotgdFormat.php** New file for formats functions
    -   This file create a instance in a global variable with name `$lotgdFormat`
    -   `numeral(float $number, int $decimals = 0, string $dec_point = false, string $thousands_sep = false)` format a number with grouped thousands
        -   By default if you don't pass `dec_point` and/or `$thousands_sep` use the game settings values.
-   **lib/pageparts.php** Now var `$html` is global. You can use in your modules for add your content to templates
-   **lib/template.class.php**
    -   New file that contain a base class `LotgdTemplate` with functions for templates
    -   New filter `relativedate` show a relative date from now
    -   New filter `lotgd_popup` generate a popup link
    -   New filter `nltoappon` convert all line breaks to LOTGD style
    -   New filter `numeral` format a number with grouped thousands
    -   All theme templates now obtain the `user` variable that contains the user information as well as `$session['user']`
        -   Note: Keep in mind that the information you get is the most up to date.
    -   **Note** Now by default yout `LotgdTemplate` is this base class for templates in LOTGD and not load innecesary functions of `LotgdTheme`
-   **template.php** Class `LotgdTheme` that only contain funcions for themes of LOTGD extends base class of `LotgdTemplate`
-   **creatures.php** Now the creatures can have a description and image, both are optional.
-   **Jaxon-php** In the folder `jaxon` you can place your classes in order to use Ajax globally in LOTGD
-   **lib/playerfunctions.php** New functions:
    -   `explained_row_get_player_attack` and `explained_row_get_player_defense` with this functions can get a raw info

### DEPRECATED

-   **lib/template.php**
    -   Filters:
        -   `appoencode` is now deprecated and removed in a future version, use `colorize` instead
        -   `color_sanitize` is now deprecated and removed in a future version, use `uncolorize` instead
-   **lib/datetime.php**
    -   Functions:
          `relativedate` is now deprecated and remove in a future version, use `$lotgdFormat->relativedate($indate)` instead

### REMOVES

-   **mailinfo_common.php** Reemplace for Jaxon.
-   **mailinfo_server.php** Reemplace for Jaxon.
-   **templates/mail-ajax.twig** Not used with Jaxon
-   **xajax/** Folder and content

### FIXES

-   **lib/battle/functions.php** Fixed error that did not show the correct text in a perfect fight
-   **lib/battle/extend.php** Fixed error with undefined index

### NOTES

-   **package.json** Updated/Deleted dependencies
-   **composer.json** Added a new dependencie `paragonie/random_compat` Needed in PHP 5.6 for component `Zend\Math`
-   **Optimization** Most of `.php` files have had a slight code optimization using CS Fixer.
-   **_TODO_**
    -   Create a system for replacing keywords by their value for templates. Ahem: {playername} would be replaced by the player's name.

# Version: 2.5.0

### CHANGES

-   **dragon.php** Changed order of messages:
    -   First message of slayed Dragon.
    -   First message of flawless fight
-   **clan.php** The form for create new clan are now in template system `semantic/src/themes/jade/assets/templates/pages/clan/new.twig`.
-   **rawsql.php** Now when execute a PHP or SQL code catch error and show it
-   **lib/settings.class.php**
    -   All functions are now public explicitly
    -   It is forced to save the configuration in the cache, to reduce the load of the database.
    -   Optimized for better performance
-   **lib/datacache.php** When `getdatacache` is used, check that`$duration` is a numeric value and greater than 0
-   **lib/dbwrapper.php** Functions `DB::select`, `DB::update`, `DB::insert`, `DB::delete` can support two params: `$table` and `$prefixed`, the second param is used for indicate whether you want that table name need prefixed or no, default is `TRUE`
-   **lib/template.php** Class `LotgdTemplate`, change function `__construct(array $loader = [], array $options = [])`, now can pass `loaders` and `options` for your extend class in your modules
-   **lib/output.php** Use `class="center aligned"` to center text with code ``c\`
-   **lib/about/about_listmodules.php** Now use Twig template for show table
-   **lib/battle/functions.php** Change order of messages:
    -   First name of creature and them creature die text
    -   First gems reward and them gold
    -   Message of flawless fight always at the end
-   **lib/modules.php** Now settings of a modules is forced to use data cache
-   **battle.php**
    -   Changed name of hook
        -   `battle` to `battle-turn-start` and `battle-turn-end`
    -   Changed position of `battle-turn-end`
    -   Can use hook `battle-victory-end` and `battle-defeat-end` for add data to creatures, and reflect in `battlebars-end`
-   **lib/creaturefunctions.php** New modulehook `creature-search`
    -   With this hook, you can add or remove creatures, when invoke the function `lotgd_search_creature`
-   THEME
    -   Updated Semantic UI version 2.2.10 => 2.2.14
    -   The `*.variables` files in the `Jade` theme have only the variables that have been changed.
    -   Change text color element -> divider

### FEATURES

-   **lib/template.php**
    -   Added new filter to templates `colorize` is an alias of `appoencode`
    -   Added new function to templates `isValidProtocol` check if a url string have a valid protocol `http, https, ftp, fpts`
-   **lib/settings.class.php** Added new function for get all settings of game `getAllSettings()`
-   **battle.php** Added battle options to user session

### DEPRECATED

-   Nothing

### REMOVES

-   **errorhandling.php** Removed file
    -   `magic_quotes_gpc` is DEPRECATED as of PHP 5.3.0 and REMOVED as of PHP 5.4.0
-   **http.php** It is removed from numerous files, **common.php** already includes this file

### FIXES

-   **common.php** Fixed error with undefined index
-   **pvp.php** Fixed error with name of required file, deleted unnecesary space
-   **stables.php** Fixed error with undefined index
-   **train.php** Fixed error with undefined index
-   **login.php** Fixed posible error with blank `restorepage`
-   **newday.php** Fixed error with decimals when calculate interest rate with gold in bank
-   **lib/events.php** Fixed error with index and variable undefined
-   **lib/checkban.php** Fixed error with undefined index/variable
-   **lib/settings.class.php** Fixed error in the function `saveSetting`, did not save the new data in the BD.
-   **lib/about/about_listmodules.php** Fixed an error that did not show links to downloads of the modules
-   **lib/configuration/configuration_cronjob.php** Fixed error of badnav when activate/desactivate cronjob
-   **lib/mail/{case_read.php, case_write.php}** Fixed error for not un-quotes a quoted string of "subject" and "body" of messages.
-   **lib/battle/extended.php** Fixed error with undefined index
-   **lib/graveyard/case_question.php** Fixed error with undefined variable

### NOTES

-   Add _.eslintignore_ file for ignore files in _semantic/_ folder. These files are maintained by Semantic UI
-   **lib/dbwrapper.php** Documented function `DB::query`
-   **package.json** Updated dependencies
-   **Gulp tasks** Added a new `composer` task, removes all PHP dependencies that are only used in a development environment and also optimizes the "autoloader", when use build app `gulp --env production`

# Version: 2.4.0

### CHANGES

-   **lib/battle/functions.php**
    -   Only load the taunt if death message have a taunt, have a new appearance for death message with taunt
    -   The text exp/favor gained, is only displayed if it is greater than zero.
-   **lib/commentary.php** Set autocomplete off for inputs
-   **lib/datetime.php** Optimizations
-   **lib/experience.php** Increases cache lifetime and improves cache control
-   **lib/template.php** Class `LotgdTemplate` extends class `Twig_Environment`, now is more easy extends `LotgdTemplate` for create a new class for your modules
-   **assets/components/datacache.js** Now the modal to delete by prefix has a button to cancel.
-   **create.php** Has a new structure and changes some queries by the new functions of the DB script
-   **lib/commentary.php** Optimize viewing of comments by eliminating an unnecessary extra loop
-   **lib/showform.php** Small optimization
-   **battle.php** Renamed variable name `$content` to `$lotgdBattleContent`. For now you can use `$content` in `battle.php` both are associated `$content = &$lotgdBattleContent`
-   **Theme template Jade**
    -   "Step" now has a chord color for the Jade theme
    -   "Input" fits the size of the corner label
    -   Modules
        -   Now include style for module _Worldmapen_

### FEATURES

-   **lotgd.js** JavaScript `Lotgd` now have a new functions
    -   `Lotgd.notify`. This function use _toastr_ for notifications generation.
    -   `Lotgd.confirm` Displays a confirmation dialog before going to the URL using swal

### DEPRECATED

-   **Functions**
    -   **lib/datetime.php**
        -   `getmicrotime` is unnecesary function, use `microtime(true)` instead

### REMOVES

-   **lib/mail.php** Removed unused file
-   **Functions**
    -   **lib/datetime.php**
        -   `readabletime` use `reltime` instead

### FIXES

-   **lib/commentary.php** Fixed error with data cache of `commentary-latestcommentary_`
-   **lib/datetime.php** Fixed error with `reltime` function, not show real time.
-   **shades.php** The line says, now it's translated
-   **lib/battle/functions.php** Added missing variable `$count` in a function
-   **lib/battle/buffs.php** Fixed error with undefined index
-   **cronjob.php** Fixed error with key used for cache (did not match the key to get with the update), removed unnecessary required file and avoid potential problems with other cache data and optimization/removal processes
-   **lib/datacache.php** Fixed error that in some cases it may not be possible to delete certain files and directories because they do not have permissions.
-   **lib/configuration/configuration_cronjob.php** When delete a CronJob invalidate data cache
-   **create.php** Fixed error with variables/index not defined
-   **lib/battle/extended.php** Fixed error with index names creatures and companions not share same names ^\_^
-   **lib/pvplist.php** Fixed error with HTML of table
-   **lib/showform.php** Fixed error with 'float' and 'location' field give an undefined key error
-   **stables.php** Fixed error with undefined variable
-   **mail.php**
    -   Fixed bug with text display in email
    -   Fixed issue with sending emails
-   **lib/all_tables.php** Fixed error with fields in table 'mail' was missing field 'originator'
-   **lib/creaturefuntions.php** Now all creatures have 'creaturegold' default is 0, for avoid errors in same functions
-   **train.php**
    -   Fixed error that did not show the taunt to be defeated by the master
    -   Fixed error for not working correctly 'Superuser Gain Level'
-   **battle.php** The message that shows who got the first attack is no longer shown as if it were one more round.
-   It adapts to the new format of the battle
    -   **pvp.php**
    -   **dragon.php**
-   **bank.php** Now buttons and inputs have LOTGD style
-   **lib/taunt.php** Fixed the error by not selecting a taunt with the `select_taunt` function
-   **Theme template Jade**
    -   Fix error with names of files CSS.
    -   Semantic UI element 'Steps' now have a new color pattern
    -   Character stats "charhead" element have now padding

### NOTES

-   Battle: renamed variable name `$content` to `$lotgdBattleContent`. Remember revise your modules.

# Version: 2.3.0

### CHANGES

-   **lib/template.php** code is improved not to repeat calculations
    -   Filter for translation now admit a second param for add a _namespace_
-   **lib/errorhandling.php** unactivate custom error_handling function
-   **lib/creaturesfunctions.php** and **lib/forestoutcomes.php** now set/update 'creaturemaxhealth' for the creature, this do that in battle always show de real maxhp of creature and not current hp as maxhp
-   **lib/newday/dbcleanup.php** small optimization
-   **lib/creaturefunctions.php** check if creature have AI Script
-   **lib/graveyard/case_battle_search.php** now creatures are created using function `lotgd_transform_creature`
-   **lib/cache.php** now array of options merge default array
-   **lib/dbwrapper.php** return a empty result object when query fail. With this not get error al use this functions `$queryResult->count()`, `$queryResult->current()`
-   **lib/forms.php** Removes all JavaScript from php file and remade to improve appearance and information

-   Improved the format of files of battle
    -   **lib/extended-battle.php** moved and renamed to **lib/battle/extended.php**
        -   Delete code for old battlebar
        -   Updated to reduce complexity and adapt it to the new template
    -   **lib/battle-buffs.php** moved and renamed to **lib/battle/buffs** small optimization
    -   **lib/battle-skills.php** moved and renamed to **lib/battle/skills** small optimization
    -   **lib/battle.php** now use the new template system for show all information of battle
        -   Functions `battle_player_attacks` and `battle_badguy_attacks` are moved to file **lib/battle/functions.php**
    -   Other changes in battle system:
        -   Now `battle.php` control the result of battle executing functions `battlevictory` or `battledefeat` as necessary
        -   New template `battle/battle.twig` added for show information of battle as results. This allow you to customize appearance of battle
    -   Others files changed for new battle format
        -   **forest.php**
        -   **graveyard.php**
        -   **train.php**
-   _Theme_
    -   **templates/battle/forestcreaturebar.twig** change name to **templates/battle/combathealthbar.twig** and updated

### FEATURES

-   **lib/dbwrarpper.php** add new function `DB::expression` is a shortcut for class _Zend\\Db\\Sql\\Predicate\\Expression_
-   **JavaScript**
    -   New functions
        -   `Lotgd.previewfield` Used for preview field (used for file **lib/forms.php**)
        -   `Lotgd.appoencode` Format a text with game colors
        -   `Lotgd.escapeRegex` Escape text for used in RegExp patterns
        -   `Lotgd.loadnewchat` Load new comments of chat

### DEPRECATED

-   **Functions**
    -   **lib/forestoutcomes.php**
        -   `forestvictory` and `forestdefeat` not are used anymore. `battle.php` execute functions for victory and defeat

### REMOVES

-   **lib/battle-funtions.php** delete file not in used

### FIXES

-   **dragon.php** corrected error concerning the printing of the name of the Dragon
-   **lib/pageparts.php** corrected error by which the title of the popup was not translated
-   **lib/creaturefunctions.php** now when create a new creature define `physicalresistance` stat if not defined
-   **lib/commentary.php** and **lib/forms.php** Comments can be sent again

### NOTES

-   Battle now have a new format, and have a template for customize appearance
    -   If you use `battle.php` in your modules remember make changes for compatibility with this version

# Version: 2.2.0

### CHANGES

-   **lib/dbwrapper.php** upgrade function `DB::prefix`
    -   You now have documentation
    -   Detects if it is an array to correctly add the prefix
-   **lib/creaturefunctions.php** `lotgd_generate_creature_levels` accept a param `$level` for get only stats for a creature of a determinate level
    -   Now use cache for save stats, not is necesary regenerate
-   Removed code referring to `$HTTP_GET_VARS`, `$HTTP_POST_VARS` and `$HTTP_COOKIE_VARS`
    -   **lib/http.php**
    -   **lib/errorhandling.php**
-   Add new variable to hook _clan-rank_ `$prevclanrank` indicationg previous clan rank
-   **Theme _Jade_**
    -   Template files have been rearranged
    -   **armor.php** now have a template for show a list of armors
    -   **weapons.php** now have a template for show a list of weapons

### FEATURES

-   **resources/lotgd.js**
    -   New function for data cache of games `Lotgd.datacache(optimize|clearexpire|clearall|clearbyprefix)`
    -   Add funciton, can use it with `Lotgd.swal`, show a JavaScript popup box using a SweetAlert2
-   **New CronJob system** more easy, more customizable you can add your own cronjobs very easy.
    -   Now the CronJobs system use Jobby
-   **configuration.php**
    -   New section "Cache Settings" for control data cache of game, for example: optimize and clear.
    -   New section "CronJob Settings" for control all CronJobs of game.
-   **lib/dbwrapper.php** new function `DB::pagination` create a navigation menu when you use `DB::paginator`
    -   `DB::pagination($paginator, $url));`

### DEPRECATED

-   Nothing

### REMOVES

-   **lib/creatures.php** file removed, function `creature_stats` is remplaced for `lotgd_generate_creature_levels`
-   **images/** `headbkg.GIF` and `title.gif` are deleted because not are in used.

### FIXES

-   **lib/all_tables.php** added missing field in table `accounts`
-   **lib/intaller/installer_stage_9.php** fixed possible error if xDebug or similar is installed on the server
-   **viewpetition.php** fixed error in hook, recibe a variable not defined
-   **lib/about/about_default.php** it adjusts and fixed information
-   Fixed bug not being registered on lotgd.net
    -   **lib/pageparts.php**
    -   **templates/paypal.twig**
-   **ajaxcommentary.php** now show the appropriate colors
-   **lib/datacache.php**
    -   Now use vars stored in dbconnect.php
    -   Fixed error when try to set cache directory (Incorrect function was used)

### NOTES

-   Compatibility with PHP 7 improved
-   Wiki are now translated and updated
-   Note for theme system: Everything that has to do with html / text is planned to be passed to templates. It is not intended to make a strict MVC architecture, but an approximation.

# Version: 2.1.0

### CHANGES

-   **lib/datacache.php**
    -   Now data cache system use Zend\\Cache component
    -   Can force a cache for especific data (for get and set data)
    -   All old cache functions are valid
-   **lib/newday/newday-runonce.php** optimized for new system of data cache
-   **dragon.php**
    -   Delete option to save gold from the bank (is a personalization of my other version, this can do using hooks)
    -   Now use the new function `lotgd_transform_creature` to adapted the Dragon
    -   Now the Dragon has the preset speed
-   **lib/forestoutcomes.php** now use the new function `lotgd_transform_creature`
-   **lib/dbwrapper.php** now when you use the `DB::select`,`DB::update`, `DB::insert`,`DB::delete` functions and you pass the table name, the table name is prefixed
-   **Theme**
    -   The column of stats are now out of column of content
-   **lib/output.php** posible error when try convert a string an object
-   **lib/dragonpointdspend.php** improvements in the presentation of the points spent
-   **resources/js/lotgd.js** Now are created using Webpack and have a new structure
    -   For use function of redirect post use `Lotgd.redirectPost(url, parameters)`
-   **lib/about/about_default.php** it adjusts information about
-   This files use new function `logtd_mail`
    -   **create.php**
    -   **payment.php**
    -   **lib/errorhandler.php**
    -   **lib/expire_chars.php**
    -   **lib/petition/pettion_default.php**
-   **lib/is_email.php** Now use Zend\\Validator component

### FEATURES

-   **lib/datacache.php**
    -   New functions
        -   `datacache_clearExpired` Remove expired data cache
        -   `datacache_optimize` Optimize the storage
-   **lib/creaturefunctions.php**
    -   New functions
        -   `lotgd_transform_creature` Transform creature to adapt to player.
            -   It is only to transform the creature according to the characteristics of the character
            -   Not trigger any hooks **_creatureencounter_** and **_buffbadguy_**
            -   If you want that trigger this hooks use function `buffbadguy` instead
-   **lib/data/configuration_extended.php** add new setting 'sendhtmlmail' allow send mails in html format
-   **lib/data/configuration.php** add new setting 'servername' allows you to name the server. Used for now to send mails.
-   **lib/lotgd_mail.php**
    -   Add new function `lotgd_mail` Has the same structure as the php `mail()` function. But allow send mails in html format.
-   **source.php** new _modulehook_ "source-illegal-files" for add files that you not want show code
-   **lib/http.php** new function `lotgd_base_url`

### DEPRECATED

-   Nothing

### REMOVES

-   Remove functions of files
    -   **lib/datacache.php**
        -   `recursive_remove_directory`
        -   `makecachetempname`
-   Remove functions of lotgd.js
    -   `lotdg_redirect_post`

### FIXES

-   **whostyping.php** undefined variable _name_
-   **lib/dbwrapper.php**
    -   Added missing required file
    -   Translated text when the connection fails
-   **lib/settings.php** now return a default value if not get a settings object
-   **lib/nav.php** corrected default coding (it was misspelled)
-   **lib/debuglog.php** unused variable is deleted
-   **ajaxcommentary.php** undefined index _laston_
-   **stables.php** undefined index _lad_, _lass_, _schema_
-   **lib/pageparts.php** possible unefined index
-   **lib/clan/clan_membership.php** styled using Semantic UI
-   **lib/clan/detail.php** styled using Semantic UI
-   **DataBase**, Missing tables added to database
-   Other minor bug fixes

### NOTES

-   CHANGELOG.md have a new style
-   New cache system, no need change nothing
-   Now can send mails in html format. Can configure in _Game Settings -> Extended Settings_

# Version: 2.0.1

### CHANGES

-   **Themes**
    -   Improvements in visualization
-   _Semantic UI_
    -   Personalization for LOTGD: upgrade and improvements in organization
    -   Upgrade to version 2.2.10
-   **lib/showform.php** 'notes' in forms use _color_sanitize_ function for eliminate color code
-   _Gulp tasks_ always copy installer files, because always need use in updates

### FEATURES

-   Nothing

### DEPRECATED

-   Nothing

### REMOVES

-   Nothing

### FIXES

-   **petition.php** code error that make not found files required
-   **masters** error in the name of the master level 5 by the encoding
-   **dbwrapper.php** possible security vulnerabilities with queries to the database
-   **lib/commentary.php** now show correct comments with '/me' or ':'
-   **common.php** Delete line of code for force FALSE in 'if' condition (not remember delete before ^\_^)
-   **lib/settings.class.php** _loadSettings()_ Avoid foreach if no get data
-   **creatures.php** error with new function _lotgd_generate_creature_levels_ incorrect name in file and not load file with function
-   **lib/pageparts.php** now check if 'paypal' key have code and add PayPal buttons to existed code
-   **lib/installer/intaller_stage_0.php** form have Semantic UI style
-   **lib/installer/intaller_stage_1.php** now not replace copyright of footer
-   **lib/nav.php** warnings with undefined variables
-   **create.php** now all buttons have style
-   **lib/mail/case_read.php** process color codes and correct function for translate
-   **translatortool.php** delete line of code unnecessary
-   **donators.php** now have a full style and a small optimization
-   Correct class for the tables
    -   **home.php**
    -   **templates/parts/login.twig**
    -   **templates/parts/loginfull.twig**
-   **home.php** and **templates/parts/login.twig** forget password link transfer to template

### NOTES

-   Now README.md are translated to English

# Version: 2.0.0

### CHANGES

-   Now LOTGD use **_Zend\\Db_** component for connect to database. You can access with `"DB::*"` or `"db_*"`
-   Working on compatibility with **_PHP 7.0_**

### FEATURES

-   Now LOTGD use a **_composer_** for manage external dependencies.
    		\_ Only add a dependence in a \_composer.json\* file.
-   Now LOTGD use **_Twig_** as template system. The goal is to customize certain parts of the game to fit almost any customized version of the game.
    		_ Like login form or register form.
    		_ With successive updates will increase the customization options.
    		\* Using this template system allow you to separate HTML of PHP code, increased code reading for you.
-   Now LOTGD use **_Semantic UI_** to create the UI.
    		\* With Semantic UI can personalize components and add more. And have a good structure for LOTGD.
    -   **Old system for create a theme (template) are not compatible with this version.**
-   New **function** `lotgd_generate_creature_levels()`
    		\* With this function you can generate levels for a creature base. You can use this function in your own modules. You can use `buffbadguy()` to adapt the creature.
-   New **function** `lotgd_showtabs()` You need load _lib/showtabs.php_ in your script.
    -   Do same as `lotgd_showform` but not is for show forms.

### DEPRECATED

-   **_Functions_**
    -   **lib/dbwrapper.php** this functions wil be deleted in 3.0.0 version
        -   `db_prefix` use instead `DB::prefix`
        -   `db_query` use instead `DB::query`
        -   `db_fetch_assoc` use instead `DB::fetch_assoc` but not need you can use:
            -   `$result->current()` for get 1 result or first result
            -   `foreach($result as $key => $value)` work ok
        -   `db_num_rows` use instead `DB::num_rows` but not need you can use:
            -   `$result->count()`
        -   `db_affected_rows` use instead `DB::affected_rows` but not need you can use:
            -   `$result->getAffectedRows()`
        -   `db_free_result` use instead `DB::free_result` but not need you can use:
            -   `unset($result)`
        -   `db_query_cached` use instead `DB::query_cached`
        -   `db_insert_id` use instead `DB::insert_id` but not need you can use:
            -   `$result->getGeneratedValue()`
        -   `db_error` use instead `DB::error`
        -   `db_table_exists` use instead `DB::table_exists`
        -   `db_get_server_version` use instead `DB::get_server_version`

### FIXES

-   Error of _deprecated_ mysql extension for PHP >=5.6
-   Error in _battle.php_ with references variables, _deprecated_ in PHP >=5.4
-   Error in _experience.php_ cant find exp for next level if character is in max level.

### REMOVES

-   Nothing

### NOTES

-   Now LOTGD require minium PHP 5.6 version
