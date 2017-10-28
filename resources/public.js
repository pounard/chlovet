// Public files

// @todo missing:
//      "web\/profiles\/chlovet\/themes\/resources\/js\/script.js": true,
//      "web\/sites\/all\/themes\/composer\/drupal-badm\/js\/jquery.dialog.js": true

// jQuery/jQuery.ui from node modules instead of Drupal
//import "jquery-form/jquery.form";
import "jquery.cookie";

// Drupal core
import "./drupal";
import "./jquery.once";
import "../web/misc/drupal.js";
import "../web/misc/ajax.js";
import "../web/misc/progress.js";

// Modules
import "../web/sites/all/modules/composer/drupal-ucms/ucms_seo/js/privacy-settings.js";
import "../web/sites/all/modules/composer/drupal-iresponsive/js/picturefill.min.js";
import "../web/sites/all/modules/composer/drupal-calista/js/calista.js";
import "../web/sites/all/modules/composer/drupal-udate/udate.js";
// Leaflet is supposed to be load remotly, it's better for us to use a CDN there.
import "../web/sites/all/modules/composer/drupal-latlonfield/latlonfield.leaflet.js";

// Bootstrap
//import "bootstrap/js/affix";
//import "bootstrap/js/alert";
import "bootstrap/js/button";
import "bootstrap/js/carousel";
import "bootstrap/js/collapse";
import "bootstrap/js/dropdown";
import "bootstrap/js/modal";
//import "bootstrap/js/popover";
//import "bootstrap/js/scrollspy";
//import "bootstrap/js/tab";
import "bootstrap/js/tooltip";
//import "bootstrap/js/transition";

// Front theme
import "./src/public.front";
