(function ($) {
  "use strict";

  if (!window.getComputedStyle) {
    return;
  }

  var header, headerPosition, placeholder, headerSticky = false;
  // var siblings, siblingsPosition, siblingsStyle, siblingsSticky = false;

  function onLoad() {
    // Header
    header = document.querySelector('#header');
    headerPosition = header.getBoundingClientRect();
    placeholder = document.createElement('div');
    placeholder.style.width = headerPosition.width + 'px';
    placeholder.style.height = headerPosition.height + 'px';
    // Siblings
    /*
    siblings = document.querySelector('#menu-silbings');
    siblingsPosition = header.getBoundingClientRect();
    siblingsStyle = window.getComputedStyle(siblings);
     */
  }


  function onScroll() {
    // Header
    if (header) {
      if (window.pageYOffset >= headerPosition.top && !headerSticky) {
        $(header).addClass('sticky');
        header.parentNode.insertBefore(placeholder, header);
        headerSticky = true;
      } else if (window.pageYOffset < headerPosition.top && headerSticky) {
        $(header).removeClass('sticky');
        header.parentNode.removeChild(placeholder);
        headerSticky = false;
      }
    }
    // Siblings
    /*
    if (siblings) {
      if (window.pageYOffset >= headerPosition.bottom && !siblingsSticky) {
        $(header).addClass('sticky');
        header.parentNode.insertBefore(placeholder, header);
        headerSticky = true;
      } else if (window.pageYOffset < headerPosition.bottom && siblingsSticky) {
        $(header).removeClass('sticky');
        header.parentNode.removeChild(placeholder);
        headerSticky = false;
      }
    }
     */
  }

  window.addEventListener('load', onLoad);
  window.addEventListener('scroll', onScroll);

}(jQuery));
