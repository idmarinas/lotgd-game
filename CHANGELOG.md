# Changes made for IDMarinas Edition

See CHANGELOG.txt for see changes made for Oliver Brendel +nb Edition

Visit the [Documentation](https://github.com/idmarinas/lotgd-game/wiki) for more details.  
Visit the [README](https://github.com/idmarinas/lotgd-game/blob/master/README.md).  
Visit **_V2_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V2.md)  
Visit **_V3_** [Changelog](https://github.com/idmarinas/lotgd-game/blob/master/CHANGELOG-V3.md)  

# Version: 4.5.0

### :cyclone: CHANGES

-   **Templates system**
    -   `templates/module/` are moved to `templates_modules/` folder
        -   No BC, al work as before. But from now the templates of the modules will go in this new folder.
    -   Core templates are moved to `templates_core/` folder. These templates are not intended to be customizable
    -   New config option `twig_templates_paths`
        -   Now can add your templates to Twig, and use namespace.
        -   If not want use namespace use a empty value.
        ```php
        'twig_templates_paths' => [
            'path/to/templates' => 'namespace',
            //-- If not want namespace
            'path/to/templates' => '',
        ],
        ```
    -   When you try to render a template can use this keys patterns:
        -   `{theme}` this key is replace with actual theme namespace. Example: `{theme}/path/to/template.html.twig` to `@themeJade/path/to/template.html.twig`
-   **New Page generation**
    -   New system to generate the page.
        -   Functions `page_header()` and `page_footer()` are obsoletes in 4.5.0. Deleted in version 5.0.0
        -   New system use:
            -   `LotgdResponse::pageStart(?string $title = null, ?array $parameters = [], string $textDomain = Translator::TEXT_DOMAIN_DEFAULT, ?string $locale = null)` do same as `page_header()`
                -   If only want change title of page not call other time this function use `LotgdResponse::pageTitle(string $message, ?array $parameters = [], string $textDomain = Translator::TEXT_DOMAIN_DEFAULT, ?string $locale = null)`
                -   If you call `LotgdResponse::pageStart` you activate other time the hooks and all code in `LotgdResponse::pageStart` other time, this slower the performance of page.
            -   `LotgdResponse::pageEnd(boolean $saveuser)` do same as `page_footer()`
            -   `LotgdResponse::pageAddContent($content)` this replace `rawoutput()`, add more content to page.
            -   `LotgdResponse::pageSetContent($content)` this replace all previous content with new content.
        -   Examples: can see `public/home.php` and other page to know how use this new system.

### :star: FEATURES

-   **Twig Template System**
    -   Added new core Extension: `Head`
        -   This extension added new funtions to Twig system:
            -   This new functions are based in helpers of [Laminas View](https://docs.laminas.dev/laminas-view/helpers/intro/), can see documentation for now how work.
            -   `head_link()` `<link>` element: stylesheets, feeds, favicons, trackbacks, and more.
                ```twig
                {% do head_script().appendStylesheet('/custom/module.css') %}
                ```
            -   `head_meta()` `<meta>` element is used to provide meta information about your HTML document
                ```twig
                {% do head_meta().appendHttpEquiv('Cache-Control', 'no-cache') %}
                ```
            -   `head_script()` `<script>` element is used to either provide inline client-side scripting elements or link to a remote resource.
                -   It is a simple and less complex method of adding additional code, when it is needed in a particular template.
                    -   Add files:
                    ```twig
                    {% do head_script().appendFile('path/to/file.js') %}
                    ```
                    -   Capture script:
                    ```twig
                    {% do head_script().captureStart() %}
                        var action = '/';
                        $('foo_form').action = action;
                    {% do head_script().captureEnd() %}
                    ```
            -   `head_style()` `<style>` element is used to include CSS stylesheets inline
                -   Add content
                ```twig
                {% set finalStyles = 'styles code' %}
                {% do head_style().appendStyle(finalStyles) %}
                ```
                -   Capture content
                ```twig
                {% do head_style().captureStart() %}
                body {
                    background-color: 'black';
                }
                {% do head_style().captureEnd() %}
                ```
            -   `head_title()` `<title>` element is used to provide a title for an HTML document
                ```twig
                {% do head_title('Change to new title', 'SET') %}
                {% do head_title('Append title', 'APPEND') %}
                {% do head_title('Prepend title', 'PREPEND') %}
                ```
                -   By default always append title.
            -   `inline_script()` work like `head_meta()` but add content before tag `</body>`
                -   Add files:
                ```twig
                {% do inline_script().appendFile('path/to/file.js') %}
                ```
                -   Capture script:
                ```twig
                {% do inline_script().captureStart() %}
                    var action = '/';
                    $('foo_form').action = action;
                {% do inline_script().captureEnd() %}
                ```
    -   Allow to remove or override some extension of core.
        -   For this only need added a key for a extension.
        -   Extension with a key name, can override or remove.
        ```php
        'twig_extensions' => [//-- Custom extensions for Twig
            Lotgd\Core\Twig\Extension\GameCore::class,
            // ...

            //-- Added in version 4.1.0
            // Allows to override/remove this extensions.
            Lotgd\Core\Twig\Extension\Form\Form::class=> Lotgd\Core\Twig\Extension\Form\Form::class,
            Lotgd\Core\Twig\Extension\Form\FormElement::class=> '', //-- Deleted extension
            Lotgd\Core\Twig\Extension\Form\FormElementError::class=> Lotgd\Local\Twig\Extension\Custom\FormElementError::class, //-- Override extension
            //--
        ],
        ```

-   **Webpack Encore**
    -   `encore_entry_script_tags()` and `encore_entry_link_tags()` now need pass second argument (package).
        -   `encore_entry_script_tags(string $entryName, string $packageName = null, string $entrypointName = '_default')`
        -   `encore_entry_link_tags(string $entryName, string $packageName = null, string $entrypointName = '_default')`
        -   Example:

            `encore_entry_script_tags('semantic_ui', 'lotgd', 'lotgd')`

            `encore_entry_link_tags('semantic_ui', 'lotgd', 'lotgd')`

### :fire: DEPRECATED

-   Twig extension:
    -   Functions:
        -   `page_title()` is obsolete, use `headTitle()` added in new extension.
-   Class `Lotgd\Core\Template\Base` are deprecated use `Lotgd\Core\Template\Template` instead.
-   **lib/pageparts.php** the following funtions are marked as obsoleted.
    -   `page_header()` use `\LotgdResponse::pageStart(?string $title = null, ?array $parameters = [], string $textDomain = Translator::TEXT_DOMAIN_DEFAULT, ?string $locale = null)` instead
    -   `page_footer()` use `\LotgdResponse::pageEnd()` instead
-   Class `Lotgd\Core\Db\Dbwrapper` are deprecated use Doctrine to create queries to Data Base

### :wrench: FIXES

-   **Webpack Encore**
    -   `encore_entry_script_tags()` and `encore_entry_link_tags()` now do not print duplicate files.

### :x: REMOVES

-   **public/source.php** This section now render with Jaxon, so, no needed this page.

### :notebook: NOTES

-   **Added lazy services**.
    -   These services are not always necessary, so they are only created the first time they are needed.
        -   `Lotgd\Core\Output\Censor`
        -   `Lotgd\Core\Output\Commentary`
        -   `Lotgd\Core\Pvp\Listing`
-   **Jaxon-PHP** Migrating Jaxon-Core from 2.2.* to version 3.2.*
-   **composer.json** Updated/Added/Deleted dependencies
-   **package.json** Updated/Added/Deleted dependencies

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
