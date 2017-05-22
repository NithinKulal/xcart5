/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Search pabel functionality
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var searchCallback = function ($form, linked) {
  var $linked = jQuery(linked).get(0).itemsListController;
  $linked.cleanURLParams();

  $form.find(':input').not('button').each(function (id, elem) {
    if ('action' !== jQuery(elem).attr('name') && 'returnURL' !== jQuery(elem).attr('name')) {

      var value = jQuery(elem).val();
      var skipParam = false;

      if (value === null) {
        value = '';
      }

      if (
          'checkbox' === jQuery(elem).attr('type')
          && false == jQuery(elem).prop('checked')
      ) {
          value = '';
      }

      if (
          'radio' === jQuery(elem).attr('type')
          && false == jQuery(elem).prop('checked')
      ) {
          skipParam = true;
      }

      if (!skipParam) {
        if (value && jQuery.isArray(value)) {
          for (var x in value) {
            $linked.setURLParam(jQuery(elem).attr('name').replace('[]', '[' + x + ']'), value[x]);
          }

        } else {
          $linked.setURLParam(jQuery(elem).attr('name'), value);
        }
      }
    }
  });

  $linked.loadWidget(function (content){
    var newFormId = jQuery('.search-conditions-box', content).closest('form').find('input[name="xcart_form_id"]').val();
    $form.find('input[name="xcart_form_id"]').val(newFormId);
  });
};

var SearchConditionBox = function (submitFormFlag)
{
  var makeSubmitFormFlag = !_.isUndefined(submitFormFlag) && (submitFormFlag === true);

  // Switch secondary box visibility
  jQuery('.search-conditions-box .arrow').click(
    function () {
      var searchConditions = jQuery('.search-conditions-box');
      if (searchConditions.hasClass('full')) {
        searchConditions.removeClass('full')
      } else {
        searchConditions.addClass('full')
      }
    }
  );

  // Delete filter with confirmation
  jQuery('.saved-filter-options .delete-filter').click(
    function () {
      return confirm(core.t('Are you sure you want to delete this filter?'));
    }
  );

  // Add some additional functionality for the search conditions boxes
  jQuery('.search-conditions-box').each(
    function () {
      var $this = jQuery(this);
      var linked = core.getCommentedData($this, 'linked');

      if (jQuery(linked).length > 0) {
        var $form = $this.parents('form').eq(0);

        $form.submit(
          function (event) {
            // event.stopImmediatePropagation();
            event.preventDefault();

            var formAction = jQuery('input[name="action"]', $form).eq(0).val();

            jQuery.ajax({
              type:   $form.attr('method'),
              url:    $form.attr('action'),
              data:   $form.serialize(),
              success: function (data)
              {
                if (formAction == 'search' || formAction == 'searchItemsList') {
                  searchCallback($form, linked);
                } else {
                  location.reload();
                }
              }
             });

            return false;
          }
        );

        if (makeSubmitFormFlag) {
          $form.submit();
        }
      }
    }
  );

    // Scroll to items list anchor if search is running
    if (
      (self.location + '').search(/searched=1/) != -1
      && !self.location.hash
    ) {
      var a = null;

      jQuery('.search-conditions-box').each(
        function() {
          jQuery(this).parents('form').eq(0).nextAll().each(
            function () {
              if (!a) {
                var tmp = jQuery(this).find('a.list-anchor');
                if (tmp.length) {
                  a = tmp;
                }
              }
            }
          );
        }
      );

      if (a) {
        self.location.hash = a.attr('name');
      }

    }

    // Expand secondary box if box has filled fields
    var boxes = jQuery('.search-conditions-box:not(.full) .search-conditions-hidden');
    if (boxes.length) {
      boxes.each(
        function() {
          var filled = false;
          var parentBlock = jQuery(this).parents('.search-conditions-box').eq(0);
          if (0 < parentBlock.length && true != core.getCommentedData(parentBlock, 'hideAdditionalFields')) {
            jQuery(this).find('input[type="text"],input[type="checkbox"]:checked,select,textarea').each(
              function() {
                if (jQuery(this).val()) {
                  if (jQuery(this).attr('id') == 'stateSelectorId') {
                    if (
                      jQuery(this).data('value') != ''
                      && jQuery('#country').val()
                      && statesList[jQuery('#country').val()]
                    ) {
                      filled = true;
                    }
                  } else {
                    filled = true;
                  }
                }
              }
            );

            if (filled) {
              parentBlock.addClass('full');
            }
          }
        }
      );
    }
};

jQuery().ready(SearchConditionBox);
