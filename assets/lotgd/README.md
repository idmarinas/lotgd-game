# How work new assets

`assets/` now save all CSS/JS/IMG and other content.

### How create new theme?

Need this folders
-   `assets/lotgd/css/core/` All content
    > Need change `@imagePath` in this file
    >
    > `assets/lotgd/css/core/globals/site.variables`
    >
    > To:
    >
    > `@imagePath : '../../../../../assets/NAME_OF_DIR/css/core/assets/images';`

-   `assets/lotgd/css/site/` All content

Need this files is
-   `assets/lotgd/lotgd.less`
-   `assets/lotgd/theme.config`
-   `assets/lotgd/theme.less`

In `assets` folder can include your js and other content

Them need create a custom `webpack.config.custom.js` (See: `webpack.config.custom-example.js`) for build your theme and JS
