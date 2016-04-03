api = 2
core = 7.x

defaults[projects][subdir] = "contrib"

; Drupal core
projects[drupal][type] = core
projects[drupal][patch][] = "https://www.drupal.org/files/issues/1545964-38-engine_inheritance_fixed.patch"

; Translation
projects[potx][version] = 3.0-alpha2
projects[l10n_update][version] = 2.0

; Contribution
projects[ckeditor][version] = 1.17

; Other
projects[jquery_update][version] = 3.0-alpha3
projects[entitycache][version] = 1.5

; Cache
projects[redis][version] = 3.11

; System
projects[session_proxy][version] = 1.0-rc1
