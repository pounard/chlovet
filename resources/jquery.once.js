(function ($) {
  "use strict";

  function checkId(id) {
    id = id || 'once';
    if (typeof id !== 'string') {
      throw new TypeError('The jQuery Once id parameter must be a string');
    }
    return id;
  }

  $.fn.once = function (id) {
    // Build the jQuery Once data name from the provided ID.
    var name = 'jquery-once-' + checkId(id);
    // Find elements that don't have the jQuery Once data applied to them yet.
    return this.filter(function () {
      return $(this).data(name) !== true;
    }).data(name, true);
  };

  $.fn.removeOnce = function (id) {
    // Filter through the elements to find the once'd elements.
    return this.findOnce(id).removeData('jquery-once-' + checkId(id));
  };

  $.fn.findOnce = function (id) {
    // Filter the elements by which do have the data.
    var name = 'jquery-once-' + checkId(id);
    return this.filter(function () {
      return $(this).data(name) === true;
    });
  };

  console.log("$.fn.once loaded");
}(jQuery));
