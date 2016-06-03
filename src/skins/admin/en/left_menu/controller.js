/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Left menu controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'left-menu-compressed-box',
  function() {
    return jQuery('#leftMenu.compressed .menu li').filter(
      function () {
        return jQuery(this).children('.box').length > 0;
      }
    );
  },
  function()
  {
    var element = this;
    pullUpBox(element);
    jQuery(window).resize(function() {
      pullUpBox(element);
    });
    jQuery(window).scroll(function() {
      pullUpBox(element);
    });

    core.bind('left-menu-compressed', function() {
      pullUpBox(element);
    });
  }
);

function pullUpBox(menuItem) {

    //get position data
    var box = jQuery(menuItem).children('.box');
    var arrow = box.children('.arr');
    box.css('visibility', 'hidden');
    box.css('display', 'block');
    box.css('top', '');
    arrow.css('top', '');
    var boxBottom = box.offset().top + box.height();
    var boxTop = box.offset().top;
    var viewportBottom = window.pageYOffset + document.documentElement.offsetHeight;
    var viewportTop = window.pageYOffset;
    box.css('visibility', '');
    box.css('display', '');

    //calculate  modifier and move
    if (boxBottom > (viewportBottom - 10)) {
      var position = viewportBottom - boxBottom;
      box.css('top', position);
      arrow.css('top', parseFloat(arrow.css('top')) - position);

    } else if (boxTop < (viewportTop + 10)) {
      var position = viewportTop - boxTop;
      box.css('top', position);
      arrow.css('top', parseFloat(arrow.css('top')) - position);
    }
}

core.microhandlers.add(
  'left-menu-submenu-reposition',
  function() {
    return jQuery('#leftMenu .quick-links li').filter(
      function () {
        return jQuery(this).children('.box').length > 0;
      }
    );
  },
  function()
  {
    var box = jQuery(this).children('.box');

    jQuery(this).mouseover(
      function(event) {
        if (jQuery('body.left-menu-compressed').length) {
          box.css('left', '');
          box.children('.arr').eq(0).css('left', '');

        } else {

          var li = jQuery(this);

          var wb = box.outerWidth();
          if (li.offset().left > wb / 2 && wb > li.outerWidth()) {

            // center
            var diff = Math.round((wb - li.outerWidth()) / 2);
            box.css('left', (diff * -1) + 'px');

            var arr = box.children('.arr').eq(0);
            arr.css('left', Math.round(diff + li.outerWidth() / 2) + 'px');
          }
        }
      }
    );
  }
);

core.microhandlers.add(
  'left-menu-submenu-preexpand',
  function() {
    return jQuery('#leftMenu .menu li.pre-expanded').filter(
      function () {
        return jQuery(this).children('.box').length > 0;
      }
    );
  },
  function()
  {
    jQuery(this)
      .removeClass('pre-expanded')
      .addClass('expanded')
      .children('.box')
      .show();
  }
);

core.microhandlers.add(
  'left-menu-submenu-switch',
  function() {
    return jQuery('#leftMenu .menu .link').filter(
      function () {
        return jQuery(this).parent().nextAll('.box').length > 0;
      }
    );
  },
  function()
  {
    var li = jQuery(this).parents('li').eq(0);
    jQuery(this).click(
      function() {

        if (jQuery('#leftMenu').hasClass('compressed')) {
          return true;
        }

        if (li.hasClass('expanded')) {
          li.children('.box').hide(
            'blind',
            {},
            _.bind(
              function() {
                jQuery(this).removeClass('expanded');
                core.trigger('layout.sidebar.changeHeight');
              },
              li.get(0)
            )
          );

        } else {
          li.children('.box').show(
            'blind',
            {},
            _.bind(
              function() {
                jQuery(this).addClass('expanded');
                core.trigger('layout.sidebar.changeHeight');
              },
              li.get(0)
            )
          );
        }

        return false;
      }
    );
  }
);

core.microhandlers.add(
  'left-menu-ctrl',
  '.left-menu-ctrl a',
  function () {
    var link = jQuery(this);

    link.click(
      function() {
        var box = jQuery('body');
        if (box.hasClass('left-menu-compressed')) {
          box.removeClass('left-menu-compressed');
          setTimeout(
            function() {
              jQuery('#leftMenu').removeClass('compressed');
              jQuery('#leftMenu li.has-expanded')
                .addClass('expanded')
                .removeClass('has-expanded');
            },
            250
          );
          jQuery.cookie('XCAdminLeftMenuCompressed', 0);

        } else {
          box.addClass('left-menu-compressed');
          jQuery('#leftMenu').addClass('compressed');
          jQuery('#leftMenu li.expanded')
            .addClass('has-expanded')
            .removeClass('expanded');
          jQuery('#leftMenu .menu div.box').removeAttr('style');
          jQuery.cookie('XCAdminLeftMenuCompressed', 1);
          core.trigger('left-menu-compressed');
        }

        return false;
      }
    );

  }
);
