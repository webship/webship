api = 2
core = 8.x

defaults[projects][subdir] = "contrib"

projects[uikit][type] = theme
projects[uikit][version] = 3.0-rc3

projects[webtheme][type] = theme
projects[webtheme][version] = 1.0-alpha1

projects[uikit_admin][type] = theme
projects[uikit_admin][version] = 2.0-rc3

projects[uikit_components][type] = module
projects[uikit_components][version] = 3.0-beta5
; 2918235-4
projects[uikit_components][patch][] = https://www.drupal.org/files/issues/php_fatal_error_class-2918235-4.patch

projects[uikit_gui][type] = module
projects[uikit_gui][version] = 1.x-dev

projects[ctools][type] = module
projects[ctools][version] = 3.0

projects[entity][type] = module
projects[entity][version] = 1.0-beta1

projects[config_update][type] = module
projects[config_update][version] = 1.4

projects[token][type] = module
projects[token][version] = 1.0

projects[page_manager][type] = module
projects[page_manager][version] = 4.0-beta2

projects[panels][type] = module
projects[panels][version] = 4.2

projects[ds][type] = module
projects[ds][version] = 3.1

projects[field_group][type] = module
projects[field_group][version] = 3.0-beta1

projects[smart_trim][type] = module
projects[smart_trim][version] = 1.0

projects[pathauto][type] = module
projects[pathauto][version] = 1.0

projects[features][type] = module
projects[features][version] = 3.5