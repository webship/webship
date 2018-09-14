api = 2
core = 8.x
defaults[projects][subdir] = "contrib"

projects[config_update][type] = module
projects[config_update][version] = 1.5

projects[libraries][type] = module
projects[libraries][version] = 3.0-alpha1

projects[adminimal_theme][type] = theme
projects[adminimal_theme][version] = 1.3

projects[admin_toolbar][type] = module
projects[admin_toolbar][version] = 1.24

projects[adminimal_admin_toolbar][type] = module
projects[adminimal_admin_toolbar][version] = 1.8

projects[ctools][type] = module
projects[ctools][version] = 3.0

projects[entity][type] = module
projects[entity][version] = 1.0-beta4

projects[token][type] = module
projects[token][version] = 1.4

projects[page_manager][type] = module
projects[page_manager][version] = 4.0-beta3

projects[panels][type] = module
projects[panels][version] = 4.3

projects[ds][type] = module
projects[ds][version] = 3.1
; 2779243: Method declaration incompatibility.
projects[ds][patch][] = https://www.drupal.org/files/issues/ds-method-declaration-incompatible-2779243-17.patch
; 2920868: Fixed fatal errors when we have missing layouts, on an update.
projects[ds][patch][] = https://www.drupal.org/files/issues/error-when-layout-is-removed-2920868-4.patch
; 2966959: Fix a DS issue on a Value Conflict with Layout Builder.
projects[ds][patch][] = https://www.drupal.org/files/issues/2018-04-27/2966959-ds-namespace_form_for_layout_builder-4.patch

projects[field_group][type] = module
projects[field_group][version] = 3.0-beta1

projects[smart_trim][type] = module
projects[smart_trim][version] = 1.1

projects[pathauto][type] = module
projects[pathauto][version] = 1.3

projects[uikit][type] = theme
projects[uikit][version] = 3.x-dev

projects[webtheme][type] = theme
projects[webtheme][version] = 1.0-alpha1

projects[features][type] = module
projects[features][version] = 3.8

projects[webform][type] = module
projects[webform][version] = 5.0-rc21

projects[webpage][type] = module
projects[webpage][version] = 1.x-dev