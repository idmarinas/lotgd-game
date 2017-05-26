# Changes made for Iván Diaz - IDMarinas Edition #
See CHANGELOG.txt for see changes made for Oliver Brendel +nb Edition

Visit the [Documentation](https://bitbucket.org/idmarinas/lotgd-game/wiki/Home) for more details.
Visit the [README](https://bitbucket.org/idmarinas/lotgd-game/src/master/README.md?fileviewer=file-view-default).

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
