[![pipeline status](https://git.drupalcode.org/project/webship/badges/12.0.x/pipeline.svg)](https://git.drupalcode.org/project/webship/-/pipelines)

# Webship

[![](https://www.drupal.org/files/styles/grid-2/public/WebshipCo-Large-V3-Logo-Color-with-padding.png)](http://drupal.org/project/webship)

Webship helps web developers ship websites in a swift way.

On 12.0.x, Webship is the installer profile of the [Website](https://www.drupal.org/project/website) project.
It lists the `Site` recipes of the project, applies the chosen site template, then uninstalls itself:

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

### With the project template (recommended)

Start from the [Website](https://www.drupal.org/project/website) project
template: it requires this installer and its site templates, so nothing is
downloaded during the install. Composer, PHP, Drush and the database all run
inside [DDEV](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/),
so nothing is needed on your machine but DDEV itself:

```shell
mkdir my-website && cd my-website
ddev config --project-type=drupal --docroot=web
ddev start
ddev composer create-project drupal/website:^1.0
ddev restart
ddev launch
```

`ddev restart` picks up the `.ddev/config.yaml` the template ships, which
replaces the one `ddev config` wrote. The project name is not in that file:
DDEV takes it from the directory, so the site is at
`https://<directory>.ddev.site`.

The installer asks for the site name, the site template and the administrator account.

Or install from the command line, with the site template of your choice:

```shell
ddev drush site:install ../recipes/webship_starter -y --account-name=webmaster --site-name="My Website"
```

The path is relative to the Drupal root.

### On an existing DDEV project

Add the installer to a Drupal project of your own, then install its profile:

```shell
ddev composer require webship/webship:~12.0
ddev drush site:install webship -y --account-name=webmaster --site-name="My Website"
```

The site template it applies has to be in the project's recipes directory, or
be one of the curated entries in `site-templates.yml`, which are required with
Composer during the install.

### Without DDEV

The same two paths work with a Composer, PHP and database stack of your own —
PHP 8.4 and a MySQL or MariaDB database: `composer create-project
drupal/website:^1.0 my-website`, then `vendor/bin/drush site:install
../recipes/webship_starter -y`.

## Tests

The PHPUnit tests in `tests/src` install the site with Drush, the core install command and the interactive
installer. The browser tests of the site template live in the Webship Starter.
