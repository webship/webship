[![pipeline status](https://git.drupalcode.org/project/webship/badges/12.0.x/pipeline.svg)](https://git.drupalcode.org/project/webship/-/pipelines)

# Webship

[![](https://www.drupal.org/files/styles/grid-2/public/WebshipCo-Large-V3-Logo-Color-with-padding.png)](http://drupal.org/project/webship)

Webship helps web developers ship websites in a swift way.

On 12.0.x, Webship is the installer profile of the [Website](https://www.drupal.org/project/website) project,
forked from the [Drupal CMS installer](https://www.drupal.org/project/drupal_cms_installer) 2.1.x. It lists the `Site`
recipes of the project, applies the chosen site template, then uninstalls itself:

- [Webship Starter](https://www.drupal.org/project/webship_starter): the Webship site template, with documentation,
  products and releases, a newsletter and social sharing. It holds what the Webship 11.0.x distribution installed.
- [Website Starter](https://www.drupal.org/project/website_starter): a company or product website.

## Built with

Webship and the Website Starter use the [UI Suite UIkit](https://www.drupal.org/project/ui_suite_uikit) theme,
with [UIkit](https://getuikit.com) and [HTMX](https://htmx.org), on top of Drupal and
[Display Builder](https://www.drupal.org/project/display_builder):

| Layer | Role |
|---|---|
| Drupal | Content, recipes, configuration |
| Display Builder | Page layouts and content displays, built from components |
| UI Suite UIkit | The theme: UIkit components as Drupal SDC, UI Skins, UI Styles, UI Icons |
| UIkit | CSS and JavaScript of the components |
| HTMX | Navigation without full page reloads |

No Layout Builder and no Drupal Canvas.

## Install

```shell
composer create-project drupal/website my_site
cd my_site
ddev config --project-type=drupal --docroot=web
ddev start
ddev launch
```

The installer asks for the site name, the site template and the administrator account.

From the command line, the Webship Starter site template is applied:

```shell
ddev drush site:install webship -y
```

## Tests

The PHPUnit tests in `tests/src` install the site with Drush, the core install command and the interactive
installer. The browser tests of the site template live in the Webship Starter.
