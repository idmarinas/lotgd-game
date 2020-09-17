# Translations files

In this folder go all the translation files for the different languages following the following structure:
-   Used `.yaml` files to store translations.
    -   Files are in `data/translation/[LOCALE]/[SCOPE]/[DOMAIN].yaml`
        -   `[LOCALE]` It's a folder with the name of the location.  The game uses by default languages with two characters. Example "en", "es", "fr"...
        -   `[SCOPE]` It's a folder with name for a scope of file. You can look at the other folders created to get an idea of how they are used. It is mainly for organization and identification of the translation file.
        -   `[DOMAIN].yaml` It is the name of the file with the extension `.yaml`
        -   By default have this main scopes:
            -   `app` This is where the translations files of main CORE are stored.
            -   `modules` This is where the translation files of the modules are stored.
            -   `navigation` This is where the translation files of the navigation menus are stored.
            -   `pages` This is where the translation files from the main pages are stored.
            -   `partial` This is where the translations files for partials parts. (Example, deathmessages, taunts)
    -   The translations are automatically loaded by the translation factory.
        -   It is possible to have more scopes besides `pages`, `modules`... but remember the structure of the folder `data/translation`.
-   `.yaml` files support a nesting system (array)
    -   Example:
        >     The scheme used is:
        >          key:
        >              key2:
        >                  key3: 'value'
        >              key4:
        >                  - 'value1'
        >                  - 'value2'
        >              key5:
        >                  0: 'value3'
        >                  1: 'value4'
        >              key6:
        >                  '0': 'value5'
        >                  '1': 'value6'
        >              key7:
        >                  '00': 'value7'
        >                  '01': 'value8'
        >
        >     Becomes:
        >          'key.key2.key3' => 'value',
        >          'key.key4' => [
        >             0 => 'value1',
        >             1 => 'value2'
        >          ],
        >          'key.key5' => [
        >             0 => 'value3',
        >             1 => 'value4'
        >          ],
        >          'key.key6' => [
        >             0 => 'value5',
        >             1 => 'value6'
        >          ],
        >          'key.key7.00' => 'value7',
        >          'key.key7.01' => 'value8',


New format for large text (avoid use array like before)

key: `>`
> Replaces line breaks with blanks

key: `|`
> Preserves line breaks

Can add `-` or `+` to `>` or `+`
-   `-` Not add line break to end
-   `+` Add 2 line breaks to end

See: https://symfony.com/doc/4.4/components/yaml/yaml_format.html#strings

To see all line break need add filter Twig filter "nl2br" when show text
If the Twig translation filters are used, this is done automatically
-   |t
-   |trans
-   |tl
-   |tmf
-   |tst
