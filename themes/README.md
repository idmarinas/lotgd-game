# Themes folder

This folder only contain the custom themes for your version.

## Theme structure

```
AcmeTheme
├── theme.json
├── public
│   └── asset.jpg
├── templates
│   ├── bundles
│   │   └── AcmeBundle
│   │       └── bundleTemplate.html.twig
|   └── template.html.twig
└── translations
   └── messages.en.yml
```

## Theme config reference (theme.json)
```json
{
    "name": "vendor/name-theme",
    "title": "Great theme!",
    "description": "Optional description",
    "authors": [
        {
            "name": "Example name",
            "email": "example@email.com",
            "homepage": "https://lotgd.infommo.es",
            "role": "Developer"
        }
    ],
    "parents": [
        "lotgd/core-theme-modern",
        "vendor/not-so-cool-looking-theme"
    ]
}
```

## TIP

When create your theme configure your theme.json to use as parent, with this, only need add templates that you want change.
Theme system use de parent templates when not find custom.

```json
{
    "name": "vedor/custom-theme",
    "parents": ["lotgd/core-theme-modern"]
}
```

More info of Theme System in [SyliusThemeBundle](https://github.com/Sylius/SyliusThemeBundle/tree/master/docs)
