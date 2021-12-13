# Translation Resource/File Names and Locations

Symfony Framework looks for translations files in the following default locations:

-   `translations/` directory (at the root of the project);
-   `Resources/translations/` directory inside of any bundle.

The locations are listed here with the highest priority first. That is, you can override the translation messages of a bundle in the first directory.

The override mechanism works at a key level: only the overridden keys need to be listed in a higher priority message file. When a key is not found in a message file, the translator will automatically fall back to the lower priority message files.

The filename of the translation files is also important: each message file must be named according to the following path: `domain.locale.loader`:

-   **domain**: Domains are a way to organize messages into groups. 
-   **locale**: The locale that the translations are for (e.g. en_GB, en, etc);
-   **loader**: How Symfony should load and parse the file (e.g. xlf, php, yaml, etc).

The loader can be the name of any registered loader. By default, Symfony provides many loaders:

-   `.yaml:` YAML file
-   `.xlf:` XLIFF file;
-   `.php:` Returning a PHP array;
-   `.csv:` CSV file;
-   `.json:` JSON file;
-   `.ini:` INI file;
-   `.dat`, `.res:` ICU resource bundle;
-   `.mo:` Machine object format;
-   `.po:` Portable object format;
-   `.qt:` QT Translations XML file;

The choice of which loader to use is entirely up to you and is a matter of taste. The recommended option is to use YAML for simple projects and use XLIFF if you're generating translations with specialized programs or teams.

> Note: LoTGD Core organize translations files into locales folder

More info in [Symfony Translation component](https://symfony.com/doc/5.2/translation.html)
Other info [Symfony Yaml](https://symfony.com/doc/5.2/components/yaml/yaml_format.html#strings)
