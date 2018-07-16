# Changes made for Iván Diaz - IDMarinas Edition #
See CHANGELOG.txt for see changes made for Oliver Brendel +nb Edition

Visit the [Documentation](https://bitbucket.org/idmarinas/lotgd-game/wiki/Home) for more details.
Visit the [README](https://bitbucket.org/idmarinas/lotgd-game/src/master/README.md).

# Version: 2.6.0

### CHANGES
* **battle.php** `$options` now have a new index `endbattle` that indicate if battle end
* **motd.php**, **mail.php** and **petition.php** now open its content in a modal, and not in new window.
* **Xajax** Xajax is replaced by Jaxon-PHP a fork of Xajax
* *Templates*
    * Any templates, added filters to colorize and translate text
    * **battle.php** Template files in:
        * Note: these files were moved from their previous folder `~/content`
        * `~/pages/battle.twig`
            * Now show image and description of creature if have it
        * `~/pages/battle/combathealthbar.twig`
    * **home.php** Template files in:
        * Note: these files were moved from their previous folder `~/content`
        * `~/pages/home/login.twig`
        * `~/pages/home/loginfull.twig`
* THEME
    * Updated Semantic UI version 2.2.14 => 2.3.2
    * Some adjustments are made to improve the appearance

### FEATURES

* **lib/lotgdFormat.php** New file for formats functions
    * This file create a instance in a global variable with name `$lotgdFormat`
    * `numeral(float $number, int $decimals = 0, string $dec_point = false, string $thousands_sep = false)` format a number with grouped thousands
        * By default if you don't pass `dec_point` and/or `$thousands_sep` use the game settings values.
* **lib/pageparts.php** Now var `$html` is global. You can use in your modules for add your content to templates
* **lib/template.class.php**
    * New file that contain a base class `LotgdTemplate` with functions for templates
    * New filter `relativedate` show a relative date from now
    * New filter `lotgd_popup` generate a popup link
    * New filter `nltoappon` convert all line breaks to LOTGD style
    * New filter `numeral` format a number with grouped thousands
    * New filter `pluralize` select the plural or singular form according to the last number
    * All theme templates now obtain the `user` variable that contains the user information as well as `$session['user']`
        * Note: Keep in mind that the information you get is the most up to date.
    * **Note** Now by default yout `LotgdTemplate` is this base class for templates in LOTGD and not load innecesary functions of `LotgdTheme`
* **template.php** Class `LotgdTheme` that only contain funcions for themes of LOTGD extends base class of `LotgdTemplate`
* **creatures.php** Now the creatures can have a description and image, both are optional.
* **Jaxon-php** In the folder `jaxon` you can place your classes in order to use Ajax globally in LOTGD

### DEPRECATED

* **lib/template.php**
    * Filters:
        * `appoencode` is now deprecated and removed in a future version, use `colorize` instead
        * `color_sanitize` is now deprecated and removed in a future version, use `uncolorize` instead
* **lib/datetime.php**
    * Functions:
        `relativedate` is now deprecated and remove in a future version, use `$lotgdFormat->relativedate($indate)` instead

### REMOVES

* **mailinfo_common.php** Reemplace for Jaxon.
* **mailinfo_server.php** Reemplace for Jaxon.
* **templates/mail-ajax.twig** Not used with Jaxon
* **xajax/** Folder and content

### FIXES

* **lib/battle/functions.php** Fixed error that did not show the correct text in a perfect fight
* **lib/battle/extend.php** Fixed error with undefined index


### NOTES

* **package.json** Updated/Deleted dependencies
* **composer.json** Added a new dependencie `paragonie/random_compat` Needed in PHP 5.6 for component `Zend\Math`
* **Optimization** Most of `.php` files have had a slight code optimization using CS Fixer.
* ***TODO***
    * Create a system for replacing keywords by their value for templates. Ahem: {playername} would be replaced by the player's name.


# Version: 2.5.0

### CHANGES

* **dragon.php** Changed order of messages:
    * First message of slayed Dragon.
    * First message of flawless fight
* **clan.php** The form for create new clan are now in template system `semantic/src/themes/jade/assets/templates/pages/clan/new.twig`.
* **rawsql.php** Now when execute a PHP or SQL code catch error and show it
* **lib/settings.class.php**
    * All functions are now public explicitly
    * It is forced to save the configuration in the cache, to reduce the load of the database.
    * Optimized for better performance
* **lib/datacache.php** When `getdatacache` is used, check that`$duration` is a numeric value and greater than 0
* **lib/dbwrapper.php** Functions `DB::select`, `DB::update`, `DB::insert`, `DB::delete` can support two params: `$table` and `$prefixed`, the second param is used for indicate whether you want that table name need prefixed or no, default is `TRUE`
* **lib/template.php** Class `LotgdTemplate`, change function `__construct(array $loader = [], array $options = [])`, now can pass `loaders` and `options` for your extend class in your modules
* **lib/output.php** Use `class="center aligned"` to center text with code ``c`
* **lib/about/about_listmodules.php** Now use Twig template for show table
* **lib/battle/functions.php** Change order of messages:
    * First name of creature and them creature die text
    * First gems reward and them gold
    * Message of flawless fight always at the end
* **lib/modules.php** Now settings of a modules is forced to use data cache
* THEME
    * Updated Semantic UI version 2.2.10 => 2.2.14
    * The `*.variables` files in the `Jade` theme have only the variables that have been changed.
    * Change text color element -> divider

### FEATURES

* **lib/template.php**
    * Added new filter to templates `colorize` is an alias of `appoencode`
    * Added new function to templates `isValidProtocol` check if a url string have a valid protocol `http, https, ftp, fpts`
* **lib/settings.class.php** Added new function for get all settings of game `getAllSettings()`

### DEPRECATED

* Nothing

### REMOVES

* Nothing

### FIXES

* **common.php** Fixed error with undefined index
* **pvp.php** Fixed error with name of required file, deleted unnecesary space
* **stables.php** Fixed error with undefined index
* **train.php** Fixed error with undefined index
* **login.php** Fixed posible error with blank `restorepage`
* **lib/events.php** Fixed error with index and variable undefined
* **lib/checkban.php** Fixed error with undefined index/variable
* **lib/settings.class.php** Fixed error in the function `saveSetting`, did not save the new data in the BD.
* **lib/about/about_listmodules.php** Fixed an error that did not show links to downloads of the modules
* **lib/configuration/configuration_cronjob.php** Fixed error of badnav when activate/desactivate cronjob
* **lib/mail/{case_read.php, case_write.php}** Fixed error for not un-quotes a quoted string of "subject" and "body" of messages.
* **lib/battle/extended.php** Fixed error with undefined index
* **lib/graveyard/case_question.php** Fixed error with undefined variable


### NOTES

* Add *.eslintignore* file for ignore files in *semantic/* folder. These files are maintained by Semantic UI
* **lib/dbwrapper.php** Documented function `DB::query`
* **package.json** Updated dependencies
* **Gulp tasks** Added a new `composer` task, removes all PHP dependencies that are only used in a development environment and also optimizes the "autoloader", when use build app `gulp --env production`


# Version: 2.4.0

### CHANGES

* **lib/battle/functions.php**
    * Only load the taunt if death message have a taunt, have a new appearance for death message with taunt
    * The text exp/favor gained, is only displayed if it is greater than zero.
* **lib/commentary.php** Set autocomplete off for inputs
* **lib/datetime.php** Optimizations
* **lib/experience.php** Increases cache lifetime and improves cache control
* **lib/template.php** Class `LotgdTemplate` extends class `Twig_Environment`, now is more easy extends `LotgdTemplate` for create a new class for your modules
* **assets/components/datacache.js** Now the modal to delete by prefix has a button to cancel.
* **create.php** Has a new structure and changes some queries by the new functions of the DB script
* **lib/commentary.php** Optimize viewing of comments by eliminating an unnecessary extra loop
* **lib/showform.php** Small optimization
* **battle.php** Renamed variable name `$content` to `$lotgdBattleContent`. For now you can use `$content` in `battle.php` both are associated `$content = &$lotgdBattleContent`
* **Theme template Jade**
    * "Step" now has a chord color for the Jade theme
    * "Input" fits the size of the corner label
    * Modules
        * Now include style for module *Worldmapen*

### FEATURES

* **lotgd.js** JavaScript `Lotgd` now have a new functions
    * `Lotgd.notify`. This function use *toastr* for notifications generation.
    * `Lotgd.confirm` Displays a confirmation dialog before going to the URL using swal

### DEPRECATED

* **Functions**
    * **lib/datetime.php**
        * `getmicrotime` is unnecesary function, use `microtime(true)` instead

### REMOVES

* **lib/mail.php** Removed unused file
* **Functions**
    * **lib/datetime.php**
        * `readabletime` use `reltime` instead

### FIXES

* **lib/commentary.php** Fixed error with data cache of `commentary-latestcommentary_`
* **lib/datetime.php** Fixed error with `reltime` function, not show real time.
* **shades.php** The line says, now it's translated
* **lib/battle/functions.php** Added missing variable `$count` in a function
* **lib/battle/buffs.php** Fixed error with undefined index
* **cronjob.php** Fixed error with key used for cache (did not match the key to get with the update), removed unnecessary required file and avoid potential problems with other cache data and optimization/removal processes
* **lib/datacache.php** Fixed error that in some cases it may not be possible to delete certain files and directories because they do not have permissions.
* **lib/configuration/configuration_cronjob.php** When delete a CronJob invalidate data cache
* **create.php** Fixed error with variables/index not defined
* **lib/battle/extended.php** Fixed error with index names creatures and companions not share same names ^_^
* **lib/pvplist.php** Fixed error with HTML of table
* **lib/showform.php** Fixed error with 'float' and 'location' field give an undefined key error
* **stables.php** Fixed error with undefined variable
* **mail.php**
    * Fixed bug with text display in email
    * Fixed issue with sending emails
* **lib/all_tables.php** Fixed error with fields in table 'mail' was missing field 'originator'
* **lib/creaturefuntions.php** Now all creatures have 'creaturegold' default is 0, for avoid errors in same functions
* **train.php**
    * Fixed error that did not show the taunt to be defeated by the master
    * Fixed error for not working correctly 'Superuser Gain Level'
* **battle.php** The message that shows who got the first attack is no longer shown as if it were one more round.
* It adapts to the new format of the battle
    * **pvp.php**
    * **dragon.php**
* **bank.php** Now buttons and inputs have LOTGD style
* **lib/taunt.php** Fixed the error by not selecting a taunt with the `select_taunt` function
* **Theme template Jade**
    * Fix error with names of files CSS.
    * Semantic UI element 'Steps' now have a new color pattern
    * Character stats "charhead" element have now padding

### NOTES

* Battle: renamed variable name `$content` to `$lotgdBattleContent`. Remember revise your modules.


# Version: 2.3.0

### CHANGES

* **lib/template.php** code is improved not to repeat calculations
    * Filter for translation now admit a second param for add a *namespace*
* **lib/errorhandling.php** unactivate custom error_handling function
* **lib/creaturesfunctions.php** and **lib/forestoutcomes.php** now set/update 'creaturemaxhealth' for the creature, this do that in battle always show de real maxhp of creature and not current hp as maxhp
* **lib/newday/dbcleanup.php** small optimization
* **lib/creaturefunctions.php** check if creature have AI Script
* **lib/graveyard/case_battle_search.php** now creatures are created using function `lotgd_transform_creature`
* **lib/cache.php** now array of options merge default array
* **lib/dbwrapper.php** return a empty result object when query fail. With this not get error al use this functions `$queryResult->count()`, `$queryResult->current()`
* **lib/forms.php** Removes all JavaScript from php file and remade to improve appearance and information

* Improved the format of files of battle
    * **lib/extended-battle.php** moved and renamed to **lib/battle/extended.php**
        * Delete code for old battlebar
        * Updated to reduce complexity and adapt it to the new template
    * **lib/battle-buffs.php** moved and renamed to **lib/battle/buffs** small optimization
    * **lib/battle-skills.php** moved and renamed to **lib/battle/skills** small optimization
    * **lib/battle.php** now use the new template system for show all information of battle
        * Functions `battle_player_attacks` and `battle_badguy_attacks` are moved to file **lib/battle/functions.php**
    * Other changes in battle system:
        * Now `battle.php` control the result of battle executing functions `battlevictory` or `battledefeat` as necessary
        * New template `battle/battle.twig` added for show information of battle as results. This allow you to customize appearance of battle
    * Others files changed for new battle format
        * **forest.php**
        * **graveyard.php**
        * **train.php**
* *Theme*
    * **templates/battle/forestcreaturebar.twig** change name to **templates/battle/combathealthbar.twig** and updated

### FEATURES

* **lib/dbwrarpper.php** add new function `DB::expression` is a shortcut for class *Zend\Db\Sql\Predicate\Expression*
* **JavaScript**
    * New functions
        * `Lotgd.previewfield` Used for preview field (used for file **lib/forms.php**)
        * `Lotgd.appoencode` Format a text with game colors
        * `Lotgd.escapeRegex` Escape text for used in RegExp patterns
        * `Lotgd.loadnewchat` Load new comments of chat

### DEPRECATED

* **Functions**
    * **lib/forestoutcomes.php**
        * `forestvictory` and `forestdefeat` not are used anymore. `battle.php` execute functions for victory and defeat

### REMOVES

* **lib/battle-funtions.php** delete file not in used

### FIXES

* **dragon.php** corrected error concerning the printing of the name of the Dragon
* **lib/pageparts.php** corrected error by which the title of the popup was not translated
* **lib/creaturefunctions.php** now when create a new creature define `physicalresistance` stat if not defined
* **lib/commentary.php** and **lib/forms.php** Comments can be sent again

### NOTES

* Battle now have a new format, and have a template for customize appearance
    * If you use `battle.php` in your modules remember make changes for compatibility with this version


# Version: 2.2.0

### CHANGES

* **lib/dbwrapper.php** upgrade function `DB::prefix`
    * You now have documentation
    * Detects if it is an array to correctly add the prefix
* **lib/creaturefunctions.php** `lotgd_generate_creature_levels` accept a param `$level` for get only stats for a creature of a determinate level
    * Now use cache for save stats, not is necesary regenerate
* Removed code referring to `$HTTP_GET_VARS`, `$HTTP_POST_VARS` and `$HTTP_COOKIE_VARS`
    * **lib/http.php**
    * **lib/errorhandling.php**
* Add new variable to hook *clan-rank* `$prevclanrank` indicationg previous clan rank
* **Theme *Jade***
    * Template files have been rearranged
    * **armor.php** now have a template for show a list of armors
    * **weapons.php** now have a template for show a list of weapons

### FEATURES

* **resources/lotgd.js**
    * New function for data cache of games `Lotgd.datacache(optimize|clearexpire|clearall|clearbyprefix)`
    * Add funciton, can use it with `Lotgd.swal`, show a JavaScript popup box using a SweetAlert2
* **New CronJob system** more easy, more customizable you can add your own cronjobs very easy.
    * Now the CronJobs system use Jobby
* **configuration.php**
    * New section "Cache Settings" for control data cache of game, for example: optimize and clear.
    * New section "CronJob Settings" for control all CronJobs of game.
* **lib/dbwrapper.php** new function `DB::pagination` create a navigation menu when you use `DB::paginator`
    * `DB::pagination($paginator, $url));`

### DEPRECATED

* Nothing

### REMOVES

* **lib/creatures.php** file removed, function `creature_stats` is remplaced for `lotgd_generate_creature_levels`
* **images/** `headbkg.GIF` and `title.gif` are deleted because not are in used.

### FIXES

* **lib/all_tables.php** added missing field in table `accounts`
* **lib/intaller/installer_stage_9.php** fixed possible error if xDebug or similar is installed on the server
* **viewpetition.php** fixed error in hook, recibe a variable not defined
* **lib/about/about_default.php** it adjusts and fixed information
* Fixed bug not being registered on lotgd.net
    * **lib/pageparts.php**
    * **templates/paypal.twig**
* **ajaxcommentary.php** now show the appropriate colors
* **lib/datacache.php**
    * Now use vars stored in dbconnect.php
    * Fixed error when try to set cache directory (Incorrect function was used)

### NOTES

* Compatibility with PHP 7 improved
* Wiki are now translated and updated
* Note for theme system: Everything that has to do with html / text is planned to be passed to templates. It is not intended to make a strict MVC architecture, but an approximation.



# Version: 2.1.0

### CHANGES

* **lib/datacache.php**
    * Now data cache system use Zend\\Cache component
    * Can force a cache for especific data (for get and set data)
    * All old cache functions are valid
* **lib/newday/newday-runonce.php** optimized for new system of data cache
* **dragon.php**
    * Delete option to save gold from the bank (is a personalization of my other version, this can do using hooks)
    * Now use the new function `lotgd_transform_creature` to adapted the Dragon
    * Now the Dragon has the preset speed
* **lib/forestoutcomes.php** now use the new function `lotgd_transform_creature`
* **lib/dbwrapper.php** now when you use the `DB::select`,` DB::update`, `DB::insert`,` DB::delete` functions and you pass the table name, the table name is prefixed
* **Theme**
    * The column of stats are now out of column of content
* **lib/output.php** posible error when try convert a string an object
* **lib/dragonpointdspend.php** improvements in the presentation of the points spent
* **resources/js/lotgd.js** Now are created using Webpack and have a new structure
    * For use function of redirect post use `Lotgd.redirectPost(url, parameters)`
* **lib/about/about_default.php** it adjusts information about
* This files use new function `logtd_mail`
    * **create.php**
    * **payment.php**
    * **lib/errorhandler.php**
    * **lib/expire_chars.php**
    * **lib/petition/pettion_default.php**
* **lib/is_email.php** Now use Zend\\Validator component


### FEATURES

* **lib/datacache.php**
    * New functions
        * `datacache_clearExpired` Remove expired data cache
        * `datacache_optimize` Optimize the storage
* **lib/creaturefunctions.php**
    * New functions
        * `lotgd_transform_creature` Transform creature to adapt to player.
            * It is only to transform the creature according to the characteristics of the character
            * Not trigger any hooks ***creatureencounter*** and ***buffbadguy***
            * If you want that trigger this hooks use function `buffbadguy` instead
* **lib/data/configuration_extended.php** add new setting 'sendhtmlmail' allow send mails in html format
* **lib/data/configuration.php** add new setting 'servername' allows you to name the server. Used for now to send mails.
* **lib/lotgd_mail.php**
    * Add new function `lotgd_mail` Has the same structure as the php `mail()` function. But allow send mails in html format.
* **source.php** new *modulehook* "source-illegal-files" for add files that you not want show code
* **lib/http.php** new function `lotgd_base_url`

### DEPRECATED

* Nothing

### REMOVES

* Remove functions of files
    * **lib/datacache.php**
        * `recursive_remove_directory`
        * `makecachetempname`
* Remove functions of lotgd.js
    * `lotdg_redirect_post`

### FIXES

* **whostyping.php** undefined variable *name*
* **lib/dbwrapper.php**
    * Added missing required file
    * Translated text when the connection fails
* **lib/settings.php** now return a default value if not get a settings object
* **lib/nav.php** corrected default coding (it was misspelled)
* **lib/debuglog.php** unused variable is deleted
* **ajaxcommentary.php** undefined index *laston*
* **stables.php** undefined index *lad*, *lass*, *schema*
* **lib/pageparts.php** possible unefined index
* **lib/clan/clan_membership.php** styled using Semantic UI
* **lib/clan/detail.php** styled using Semantic UI
* **DataBase**, Missing tables added to database
* Other minor bug fixes

### NOTES

* CHANGELOG.md have a new style
* New cache system, no need change nothing
* Now can send mails in html format. Can configure in *Game Settings -> Extended Settings*



# Version: 2.0.1

### CHANGES

* **Themes**
    * Improvements in visualization
* *Semantic UI*
    * Personalization for LOTGD: upgrade and improvements in organization
    * Upgrade to version 2.2.10
* **lib/showform.php** 'notes' in forms use *color_sanitize* function for eliminate color code
* *Gulp tasks* always copy installer files, because always need use in updates

### FEATURES

* Nothing

### DEPRECATED

* Nothing

### REMOVES

* Nothing

### FIXES

* **petition.php** code error that make not found files required
* **masters** error in the name of the master level 5 by the encoding
* **dbwrapper.php** possible security vulnerabilities with queries to the database
* **lib/commentary.php** now show correct comments with '/me' or ':'
* **common.php** Delete line of code for force FALSE in 'if' condition (not remember delete before ^_^)
* **lib/settings.class.php** *loadSettings()* Avoid foreach if no get data
* **creatures.php** error with new function *lotgd_generate_creature_levels* incorrect name in file and not load file with function
* **lib/pageparts.php** now check if 'paypal' key have code and add PayPal buttons to existed code
* **lib/installer/intaller_stage_0.php** form have Semantic UI style
* **lib/installer/intaller_stage_1.php** now not replace copyright of footer
* **lib/nav.php** warnings with undefined variables
* **create.php** now all buttons have style
* **lib/mail/case_read.php** process color codes and correct function for translate
* **translatortool.php** delete line of code unnecessary
* **donators.php** now have a full style and a small optimization
* Correct class for the tables
    * **home.php**
    * **templates/parts/login.twig**
    * **templates/parts/loginfull.twig**
* **home.php** and **templates/parts/login.twig** forget password link transfer to template

### NOTES

* Now README.md are translated to English


# Version: 2.0.0

### CHANGES

* Now LOTGD use ***Zend\Db*** component for connect to database. You can access with `"DB::*"` or `"db_*"`
* Working on compatibility with ***PHP 7.0***

### FEATURES

* Now LOTGD use a ***composer*** for manage external dependencies.
	* Only add a dependence in a *composer.json* file.
* Now LOTGD use ***Twig*** as template system. The goal is to customize certain parts of the game to fit almost any customized version of the game.
	* Like login form or register form.
	* With successive updates will increase the customization options.
	* Using this template system allow you to separate HTML of PHP code, increased code reading for you.
* Now LOTGD use ***Semantic UI*** to create the UI.
	* With Semantic UI can personalize components and add more. And have a good structure for LOTGD.
    * **Old system for create a theme (template) are not compatible with this version.**
* New **function** `lotgd_generate_creature_levels()`
	* With this function you can generate levels for a creature base. You can use this function in your own modules. You can use `buffbadguy()` to adapt the creature.
* New **function** `lotgd_showtabs()` You need load *lib/showtabs.php* in your script.
    * Do same as `lotgd_showform` but not is for show forms.

### DEPRECATED

* ***Functions***
    * **lib/dbwrapper.php** this functions wil be deleted in 3.0.0 version
        * `db_prefix` use instead `DB::prefix`
        * `db_query` use instead `DB::query`
        * `db_fetch_assoc` use instead `DB::fetch_assoc` but not need you can use:
            * `$result->current()` for get 1 result or first result
            * `foreach($result as $key => $value)` work ok
        * `db_num_rows` use instead `DB::num_rows` but not need you can use:
            * `$result->count()`
        * `db_affected_rows` use instead `DB::affected_rows` but not need you can use:
            * `$result->getAffectedRows()`
        * `db_free_result` use instead `DB::free_result` but not need you can use:
            * `unset($result)`
        * `db_query_cached` use instead `DB::query_cached`
        * `db_insert_id` use instead `DB::insert_id` but not need you can use:
            * `$result->getGeneratedValue()`
        * `db_error` use instead `DB::error`
        * `db_table_exists` use instead `DB::table_exists`
        * `db_get_server_version` use instead `DB::get_server_version`

### FIXES

* Error of *deprecated* mysql extension for PHP >=5.6
* Error in *battle.php* with references variables, *deprecated* in PHP >=5.4
* Error in *experience.php* cant find exp for next level if character is in max level.

### REMOVES

* Nothing

### NOTES

* Now LOTGD require minium PHP 5.6 version
