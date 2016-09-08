# Light Translations

## Introduction

Light comes localization-ready out of the box. All that’s needed is a translation file for your language.

There are several methods to create a translation, most of which are outlined in the [WordPress Codex](https://make.wordpress.org/polyglots/handbook/). In most cases you can contribute to the project on [translate.wordpress.org](https://translate.wordpress.org/), however for creating custom translations, we find the easiest method is to use a plugin called [Loco Translate](https://wordpress.org/plugins/loco-translate/). More advanced users can use [PoEdit](https://poedit.net/).

If you want to help translate Light, please visit [translation page](https://make.wordpress.org/polyglots/handbook/tools/glotpress-translate-wordpress-org/) in WordPress.org. We will soon move over to [Translate WordPress](https://translate.wordpress.org/)

Notice! Do not place your custom translation files here. Theme will look in this directory for translations as a fallback. It is however recommended that you use the global WordPress language directory and install your translations like so: `/wp-content/languages/themes/`. That way they will not be lost or overwritten during updates. Alternatively you can put translations in your child theme: `/wp-content/themes/your-child-theme/languages/`.

## Set up WordPress in your language

Add the translations to your WordPress as follows:

    wp-content/themes/light/app/languages

## Set up WordPress in your language

WordPress needs to be told which language it should run.

    Go to: Dashboard > Settings > General and change the Site language.

Once this has been done, the shop displays in your locale if the language file exists. Otherwise, you need to create the language files.

## Changelog

##### 2016.06.10
* **Swedish** - language added.

##### 2016.06.10
* Initial release