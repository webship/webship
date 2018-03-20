api = 2
core = 8.x

defaults[projects][subdir] = "contrib"

projects[uikit][type] = theme
projects[uikit][version] = 3.0

projects[uikit_admin][type] = theme
projects[uikit_admin][version] = 3.0-rc1

projects[uikit_components][type] = module
projects[uikit_components][version] = 3.x-dev

projects[uikit_gui][type] = module
projects[uikit_gui][version] = 1.x-dev

projects[webtheme][type] = theme
projects[webtheme][version] = 1.0-alpha1

projects[ctools][type] = module
projects[ctools][version] = 3.0

projects[entity][type] = module
projects[entity][version] = 1.0-beta1

projects[config_update][type] = module
projects[config_update][version] = 1.5

projects[token][type] = module
projects[token][version] = 1.1

projects[page_manager][type] = module
projects[page_manager][version] = 4.0-beta2
;; Issue #2918564: Update 'page_manager.variant_route_filter' service according to core changes
projects[page_manager][patch][] = https://www.drupal.org/files/issues/2918564-22.patch

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