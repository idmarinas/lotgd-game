# Templates folder for themes

Create your theme folder here, for example `templates/yourTheme/**/*.html.twig`

Add `templates/yourTheme` to Twig engine
```php
//-- config/autoload/local/twig-templates-YourServer-local.php
return [
    'twig_templates_paths' => [
        //-- Remember prefixed with `theme`
        'templates/yourTheme' => 'themeYourtheme',
    ],
];
```

> Not need create all templates to your Theme only create templates that you need, if Twig not find template in your folder search in base Template `templates/lotgd`

When create your templates follow this recomendations that give Symfony:

-   Use snake case for filenames and directories (e.g. blog_posts.twig, admin/default_theme/blog/index.twig, etc.);
-   Define two extensions for filenames (e.g. index.html.twig or blog_posts.xml.twig) being the first extension (html, xml, etc.) the final format that the template will generate.
-   First, create a new Twig template called blog/_user_profile.html.twig (the _ prefix is optional, but it’s a convention used to better differentiate between full templates and template fragments).


> This is the pattern that the 'templates/lotgd' folder follows

The `_blocks` folder contains all the templates that are blocks.  
The `_partials` folder contains all the templates that are partial templates of other.  
The `_macros` folder contains all the templates that are macros.  

All files in these folders have the prefix _
