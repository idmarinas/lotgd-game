# Changes made for Iván Diaz - IDMarinas Edition #
See CHANGELOG.txt for see changes made for  Oliver Brendel +nb Edition

Visit the [Documentation](https://bitbucket.org/idmarinas/lotgd-juego/wiki/Home) for more details.

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
* New **function** `lotgd_generate_creature_levels()`
	* With this function you can generate levels for a creature base. You can use this functio in your own modules. You can use `buffbadguy()` to adapt the creature.

[FIXES]

* Error of *deprecated* mysql extension for PHP >=5.6
* Error in *battle.php* with references variables, *deprecated* in PHP >=5.4
* Error in *experience.php* cant find exp for next level if character is in max level.

[REMOVES]


[NOTES]

* Now LOTGD require minium PHP 5.6 version
