// Public files

// We need to reimport it, jQuery modules are not correctly loaded.
import "./jquery.once";

// jQuery/jQuery.ui from node modules instead of Drupal
import "jquery-ui/ui/core";
import "jquery-ui/ui/widgets/button";
import "jquery-ui/ui/widgets/datepicker";
import "jquery-ui/ui/widgets/dialog";

// Dialogs
import "../web/sites/all/themes/composer/drupal-badm/js/jquery.dialog.js";
import "../web/sites/all/modules/composer/drupal-minidialog/minidialog.js";

// Dragula must be global
window.dragula = require("expose-loader?dragula!../web/sites/all/modules/composer/drupal-dragula/dragula-3.7.2/dist/dragula.js");

// Modules
import "../web/modules/locale/locale.datepicker.js";
import "../web/sites/all/modules/composer/drupal-ulink/js/better-autocomplete/better-autocomplete.js";
import "../web/sites/all/modules/composer/drupal-ulink/js/ulink.js";
import "../web/sites/all/modules/composer/drupal-udate/udate.js";
import "../web/sites/all/modules/composer/drupal-ucms/ucms_site/dist/ucms.js";
import "../web/sites/all/modules/composer/drupal-phplayout/public/edit.js";
import "../web/sites/all/modules/composer/drupal-filechunk/filechunk.js";
import "../web/sites/all/modules/composer/drupal-unoderef/dist/unoderef.js";

// @todo ckeditor is missing
//./web/sites/all/modules/composer/drupal-ulink/js/ulink.js
//./web/sites/all/modules/composer/drupal-ulink/js/better-autocomplete/better-autocomplete.js
