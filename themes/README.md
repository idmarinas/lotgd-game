Now templates are called *themes*

Templates are now using for Twig Template Engine

# Before create a theme #
Remember that your new theme must follow the same structure as the original theme. Use dashes (-) to represent a space. When it is shown in the list of topics the (-) is replaced by space.

# How create your theme #
First you need know, is that now LOTGD use Semantic UI for create a theme. Is a powerfull UI Framework that you can personalize how you want.
You can find in *semantic/* folder. Themes are stored in *semantic/src/themes*

# 1: Twig Template #
Read how work with [Twig Templates](http://twig.sensiolabs.org/doc/1.x/). Remember that, LOTGD use 1.X version of Twig.

# 2: Semantic UI #
Read how you can personalize [Semantic UI](http://semantic-ui.com/usage/theming.html). I create 2 components lotgd.less and lotgdcolors.less for the game. Are special is only for game. But used like other components of Semantic UI

# 3: Copy base theme #
Duplicate folder *semantic/src/themes/jade* and rename with name of your theme.

# 4: Personalize templates #
Personalize templates:
* All templates are in *semantic/src/themes/jade/assets/templates*. You can modify like you want, but remember that all variables are mandatory.
* * You can change login form, register form. For example, to add more data in form.

# 5: Build #
Replace `jade` with the name of your theme.
```
$ gulp --theme jade
```

semantic/src/themes/jade/assets/templates