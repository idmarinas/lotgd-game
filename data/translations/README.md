# Translations files

In this folder go all the translation files for the different languages following the following structure:
-   Used `.yaml` files to store translations.
    -   Files are in `data/translations/[LOCALE]/[SCOPE]/[DOMAIN].yaml`
        -   By default have two main scopes:
            -   `pages` This is where the translation files from the main pages are stored.
            -   `modules` This is where the translation files are stored in the modules.
    -   The translations are automatically loaded by the translation factory.
        -   It is possible to have more scopes besides `pages` and `modules` but remember the structure of the folder `data/translations`.
-   `.yaml` files support a nesting system (array)
    -   Example:
        >     The scheme used is:
        >          'key':
        >             'key2':
        >                 'key3': 'value'
        >
        >     Becomes:
        >          'key.key2.key3' => 'value'
