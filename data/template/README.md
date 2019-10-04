# How create a theme for Legend of the Green Dragon

To create your own "Legend of the Green Dragon" theme you need to duplicate the **"data/template/jade/"** folder and the **"data/template/jade.html "** file. These files are needed to create your own LoTGD theme.

> Remember to give a name to your theme. You can use "-" as a separator, when presenting the list LoTGD will replace it with a space.

All the files in the **"data/template/jade/"** folder and the **"data/template/jade.html "** file itself extend to the base files, which are in the **"data/template/base/"** folder and **"data/template/base.html.twig "** folder.

> Take the base files as a reference when creating your own theme. **Don't modify the base templates**
>
> Note: when you create your own theme. Remember these things:
>
> -   When `LotgdTheme::renderThemeTemplate()` is called, it always looks for the template in the theme that is enabled. So if you have activated the theme "harmonic" and try to call the template "page/home.twig" and this template does not exist in the folder **"data/template/harmonic/page/home.twig "** there will be an error 500 of the server.
> -   The previous section can be problematic, when making templates for the modules. This function will search the template in **"data/template/harmonic/"**".
>     -   To fix this use the `LotgdTheme::renderModuleTemplate()` function.
>     -   This function searches for the template in the folder **"data/template/module/"**".
>     -   To avoid conflicts between modules make sure that you save the templates of the same module inside a folder with the same module name.
>     -   In the [repository](https://github.com/idmarinas/lotgd-modules) of modules you can find examples of how to make/adapt your modules.
>
> Clarifications on the functions for rendering a template:
>
> `LotgdTheme::renderThemeTemplate()` renders a template of the active theme, for which it looks for the file in the **"data/template/themeName/"** folder.
>
> -   This function obtains the following variables that can be accessed from the template:
>     -   `userPre` Has user information from the beginning of the request, since the `page_header()` or `popup_header()` function was called
>     -   `user` It has the user information at the time the function `LotgdTheme::renderThemeTemplate()` was called, the index "password" is omitted.
>     -   `session` Has the current session information, not including the "user" index, since the `page_header()` or `popup_header()` function was called.
>
> `LotgdTheme::renderModuleTemplate()` renders a template, for which it looks for the file in the folder **"data/template/module/"**".

## Module hooks in templates

> `modulehook` in templates only return an array of messages prepare to print.

Example of code in template (village.twig)

```
    {% set result = modulehook('village-header') %}
    {% for message in result %}
        {{ message[0]|t(message[1], message[2])|colorize }}
    {% endfor %}

    > The returned array has an array with messages with the same structure as the next one:

    Example of array returned of modulehook:
    [
        [
            'translation.key',
            ['params' => 'for use in translation'],
            'textDomain'
        ],
        [
            'translation.key.2',
            ['params2' => 'for use in translation2'],
            'textDomain2'
        ]
    ]
```
