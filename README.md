[![CircleCI](https://circleci.com/gh/webship/webship/tree/11.0.x.svg?style=svg)](https://app.circleci.com/pipelines/github/webship/webship/170/workflows/c4dbf5c8-4d74-4a81-ad68-c150307bb564) 11.0.0-beta1

# Webship

[![](https://www.drupal.org/files/styles/grid-2/public/WebshipCo-Large-V3-Logo-Color-with-padding.png)](http://drupal.org/project/webship)

The Webship.co portal site was built on top of Drupal, as it has many options, tools, frameworks, and configuration management, which are needed in building solutions.

## Usage

First you need to [install composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

> Note: The instructions below refer to the [global composer installation](https://getcomposer.org/doc/00-intro.md#globally).
You might need to replace `composer` with `php composer.phar` (or similar)
for your setup.

After that you can create the project:

To install the latest version of Webship ~11.0.0 run this command:
```
composer create-project webship/webship-project:11.0.0-beta1 WEBSITE_NAME --stability dev --no-interaction
```


To install the development version of Webship 11.0.x run this command:
```
composer create-project webship/webship-project:11.0.x-dev WEBSITE_NAME --stability dev --no-interaction
```
