/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product comparison table
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(
  function() {
    jQuery('button.add2cart').click(
      function() {
        // Form AJAX-based submit
        var form = this.form;

        if (form) {
          form.commonController.submitBackground()
        }
      }
    );

    jQuery('table.comparison-table tbody.data tr').not('.group').each(
      function() {
        var tr = jQuery(this);
        var td = false;
        var ident = true;
        tr.find('td').not(':first-child').each(
          function() {
            if (false === td) {
              td = jQuery(this).html();
            } else if (td != jQuery(this).html()) {
              ident = false;
            }
          }
        );
        if (ident) {
          tr.addClass('row-hidden');
        }
      }
    );

    jQuery('table.comparison-table tbody.data tr.group').each(
      function() {
        var tr = jQuery(this);
        var hide = true;
        tr.nextUntil('tr.group').each(
          function() {
            if (!jQuery(this).hasClass('row-hidden')) {
              hide = false;
            }
          }
        );
        if (hide) {
          tr.addClass('row-hidden');
        }
      }
    );

    tData = jQuery('table.comparison-table tbody.data');
    tData.addClass('diff-only');
    jQuery('input#diff').change(
      function() {
        if (jQuery(this).prop('checked')) {
          tData.addClass('diff-only');
        } else {
          tData.removeClass('diff-only');
        }
      }
    ).prop('checked', 'checked');

    jQuery('span.three-dots').mouseenter(
      function() {
        var sp = jQuery(this);
        jQuery(this).find('div').each(
          function() {
            jQuery(this).css('position', 'fixed');
            jQuery(this).css('top', sp.offset().top - jQuery(window).scrollTop() + 12);
            jQuery(this).css('left', sp.offset().left - jQuery(window).scrollLeft() + 27);
          }
        );
      }
    );

    var $table = jQuery('table.comparison-table');

    var width = 960 / Math.min(5, jQuery('tbody.header-hidden', $table).find('td').length) - 24;
    jQuery('td', $table).width(width);
    jQuery('tr.names td div', $table).width(width - 1);


    var headerFixed = jQuery('tbody.header-fixed', $table);
    var header = jQuery('tbody.header', $table);

    var headerHeight = header.height();

    var hidden_td = jQuery('tbody.header-hidden td', $table);
    var headerHiddenHeight = headerFixed.height() - (hidden_td.outerHeight() - hidden_td.height());
    hidden_td.height(headerHiddenHeight);

    jQuery('tbody.header-hidden', $table).show();
    headerFixed.addClass('sticky');

    var $window = jQuery(window);
    $window.scroll(
      function() {
        var pageHeaderHeight = getPageHeaderHeight();
        var offset = $window.scrollTop() - getHeaderFixedTop() - 1;

        var fixedPosition = -pageHeaderHeight < offset;
        var position = Math.ceil((fixedPosition ? offset + pageHeaderHeight : 0) + headerHeight);
        headerFixed.toggleClass('fixed', fixedPosition);

        headerFixed.css('top', position);
      }
    );
    $window.scroll();

    jQuery('body').mousewheel(function (event) {
      var realEvent = event.originalEvent || event;
      // remove default behavior
      realEvent.preventDefault();

      //scroll without smoothing
      var wheelDelta = -0.65 * realEvent.deltaY;
      var currentScrollPosition = window.pageYOffset;
      window.scrollTo(0, currentScrollPosition - wheelDelta);
    });

    function getPageHeaderHeight() {
      var header;
      if (jQuery('.mobile_header:visible').length) {
        header = jQuery('.mobile_header>*');

        return header.height() + header.offset().top - $window.scrollTop();

      } else if (jQuery('.desktop-header:visible').length) {
        header = jQuery('.desktop-header');

        return header.height() + header.offset().top - $window.scrollTop();
      }

      return 0;
    }

    function getHeaderFixedTop() {
      headerFixed.removeClass('sticky');
      jQuery('tbody.header-hidden', $table).hide();

      var result = headerFixed.offset().top;

      jQuery('tbody.header-hidden', $table).show();
      headerFixed.addClass('sticky');

      return result
    }
  }
);
