# Changes made for Iván Diaz - IDMarinas Edition #
See CHANGELOG.txt for see changes made for Oliver Brendel +nb Edition

Visit the [Documentation](https://bitbucket.org/idmarinas/lotgd-game/wiki/Home) for more details.
Visit the [README](https://bitbucket.org/idmarinas/lotgd-game/src/master/README.md?fileviewer=file-view-default).

## Version: 2.0.1
[CHANGES]

* **Themes**
    * Improvements in visualization
* *Semantic UI*
    * Personalization for LOTGD: upgrade and improvements in organization
    * Upgrade to version 2.2.10
* `lib/showform.php` 'notes' in forms use *color_sanitize* function for eliminate color code
* *Gulp tasks* always copy installer files, because always need use in updates

[FEATURES]

* Nothing

[FIXES]

* `petition.php` code error that make not found files required
* `masters` error in the name of the master level 5 by the encoding
* `dbwrapper.php` possible security vulnerabilities with queries to the database
* `lib/commentary.php` now show correct comments with '/me' or ':'
* `common.php` Delete line of code for force FALSE in 'if' condition (not remember delete before ^_^)
* `lib/settings.class.php` *loadSettings()* Avoid foreach if no get data
* `creatures.php` error with new function *lotgd_generate_creature_levels* incorrect name in file and not load file with function
* `lib/pageparts.php` now check if 'paypal' key have code and add PayPal buttons to existed code
* `lib/installer/intaller_stage_0.php` form have Semantic UI style
* `lib/installer/intaller_stage_1.php` now not replace copyright of footer
* `lib/nav.php` warnings with undefined variables
* `create.php` now all buttons have style
* `lib/mail/case_read.php` process color codes and correct function for translate
* `translatortool.php` delete line of code unnecessary
* `donators.php` now have a full style and a small optimization
* Correct class for the tables
    * `home.php`
    * `templates/parts/login.twig`
    * `templates/parts/loginfull.twig`
* `home.php` and `templates/parts/login.twig` forget password link transfer to template

[REMOVES]

* Nothing

[NOTES]

* Now README.md are translated to English


## Version: 2.0.0
[CHANGES]

* Now LOTGD use ***Zend\Db*** component for connect to database. You can access with `"DB::*"` or `"db_*"`
	* All functions `"db_*"` emit a deprecation warning.
	* In version 3.0.0 `"db_*"` functions will be deleted, use `"DB::*"` instead.
* Working on compatibility with ***PHP 7.0***

[FEATURES]

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
	* With this function you can generate levels for a creature base. You can use this functioN in your own modules. You can use `buffbadguy()` to adapt the creature.
* New **function** `lotgd_showtabs()` You need load *lib/showtabs.php* in your script.
    * Do same as `lotgd_showform` but not is for show forms.

[FIXES]

* Error of *deprecated* mysql extension for PHP >=5.6
* Error in *battle.php* with references variables, *deprecated* in PHP >=5.4
* Error in *experience.php* cant find exp for next level if character is in max level.

[REMOVES]

* Nothing

[NOTES]

* Now LOTGD require minium PHP 5.6 version
