/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Model selector controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    canApply: function () {
      return this.$element.is('.model-input-selector');
    },
    handler: function() {

      var $wrapper = this.$element.closest('.model-selector');
      var $element = this.$element;
      var elementNamespace = 'model-selector.' + $element.closest('.model-selector').data('type');

      $wrapper.get(0).model_selector_options = {
        getter:    core.getCommentedData($wrapper, 'getter'),
        min_count: core.getCommentedData($wrapper, 'min_count')
      };

      var doSetModelAsSelected = function()
      {
        $element.data('model-selected', 1);
        $element.closest('form').get(0).commonController.switchControlReadiness();
      };

      var doSetModelAsNotSelected = function()
      {
        $element.data('model-selected', 0);
        jQuery('input.model-value', $wrapper).val('');
        jQuery('.spinner', $wrapper).addClass('hidden');

        core.trigger(elementNamespace + '.not-selected', {
          element: $element
        });
      };

      var isModelSelected = function()
      {
        return 1 === $element.data('model-selected');
      };

      var doSelectValue = function($this)
      {
        jQuery('input.model-value', $wrapper).val($this.data('value'));
        jQuery('ul.items-list', $wrapper).addClass('hidden');

        $element.unbind('keydown', keyUpDown);

        doSetModelAsSelected();

        core.trigger(
          elementNamespace + '.selected',
          {
            element: $element,
            data: JSON.parse($this.data('data'))
          }
        );
      };

      var doPopulateList = function(data)
      {
        jQuery('ul.items-list li', $wrapper).remove();
        jQuery(data).each(function(index, elem) {
          jQuery('ul.items-list', $wrapper).append(
            '<li class="adding"><span class="text"></span><img src="images/spacer.gif" class="right-fade" alt="" /></li>'
            );

          var li = jQuery('ul.items-list li.adding', $wrapper);

          li.data('value', elem.value);
          li.data('data', JSON.stringify(elem.data));
          jQuery('span.text', li).html(elem.text_presentation);

          li.removeClass('adding');
        });
        jQuery('ul.items-list li', $wrapper)
          .eq(0)
          .addClass('active');

        var width = jQuery('input.model-input-selector', $wrapper).width() + parseInt(jQuery('input.model-input-selector', $wrapper).css('padding-right')) - 8;

        jQuery('ul.items-list > li', $wrapper).width(width);
        jQuery('ul.items-list', $wrapper).removeClass('hidden');
      };

      var keyUpDown = function(event)
      {
        var activeLI = jQuery('ul.items-list li.active', $wrapper);
        var nextActiveLI = activeLI;

        event.stopImmediatePropagation();
        if (event.which === 38 || event.which === 40 || event.which === 13 || event.which === 27) {
          event.preventDefault();
        }

        if (event.which === 38) {

          // UP
          nextActiveLI = activeLI.prev();
          if (0 === nextActiveLI.length) {
            nextActiveLI = jQuery('ul.items-list li', $wrapper).last();
          }

        } else if (event.which === 40) {

          // DOWN
          nextActiveLI = activeLI.next();
          if (0 === nextActiveLI.length) {
            nextActiveLI = jQuery('ul.items-list li', $wrapper).first();
          }

        } else if (event.which === 13 && activeLI.length > 0) {

          // ENTER
          doSelectValue(activeLI);

        } else if (event.which === 27) {

          // ESC
          jQuery('ul.items-list,.no-items-found,.enter-more-characters', $wrapper).addClass('hidden');
        }

        nextActiveLI.addClass('active');
        activeLI.removeClass('active');
      };

      var hidePopulatedList = function()
      {
        jQuery('ul.items-list,.no-items-found,.enter-more-characters', $wrapper).addClass('hidden');
        jQuery(document).unbind('click', hidePopulatedList);
      };

      this.$element.bind('keyup', function (event) {
        if (
          $element.val().length >= $wrapper.get(0).model_selector_options.min_count
          && $element.val() !== $element.data('current_search')
        ) {
          if (event.which !== 38 && event.which !== 40 && event.which !== 13 && event.which !== 27) {
            doSetModelAsNotSelected();
            event.preventDefault();
          }

          $element.data('current_search', $element.val());

          if (!isModelSelected()) {

            jQuery('.spinner', $wrapper).removeClass('hidden');
            var url = jQuery('<div/>').html($wrapper.get(0).model_selector_options.getter).text();
            core.get(
              url + '&search=' + jQuery(this).val(),
              function(XMLHttpRequest, textStatus, data) {
                var dataToShow = JSON.parse(data);

                // Inserted data is still relative
                if (dataToShow[jQuery('.model-input-selector', $wrapper).val()]) {

                  doPopulateList(dataToShow[jQuery('.model-input-selector', $wrapper).val()]);
                  jQuery('.spinner', $wrapper).addClass('hidden');

                  jQuery('.no-items-found', $wrapper).addClass('hidden');
                  if (dataToShow[jQuery('.model-input-selector', $wrapper).val()].length === 0) {
                    jQuery('.no-items-found', $wrapper).removeClass('hidden');
                  }

                  // Mouse click leads to
                  jQuery('ul.items-list li', $wrapper).bind(
                    'click',
                    function(event) {
                      doSelectValue(jQuery(this));
                    }
                  ).bind(
                    'mouseover',
                    function(event) {
                      jQuery('ul.items-list li', $wrapper).each(
                        function(index, elem) {
                          jQuery(elem).removeClass('active');
                        }
                      );
                      jQuery('ul.items-list li:hover', $wrapper).addClass('active');
                    }
                  );

                  jQuery(document).bind('click', hidePopulatedList);

                  $element
                    .unbind('keydown', keyUpDown)
                    .bind('keydown', keyUpDown)
                    .bind(
                      'keyup',
                      function(event) {
                        if (event.which === 13) {
                          event.preventDefault();

                          return false;
                        }
                      }
                    );
                }
              }
            );
          }

          jQuery('.enter-more-characters', $wrapper).addClass('hidden');
        } else if ($element.val() !== $element.data('current_search')){
          jQuery('ul.items-list,.no-items-found,.enter-more-characters', $wrapper).addClass('hidden');
        }

        if ($element.val().length < $wrapper.get(0).model_selector_options.min_count) {
          if ($element.val().length > 0) {
            jQuery('.enter-more-characters', $wrapper).html(core.t(
              'Enter X more characters to start search', {
                X: $wrapper.get(0).model_selector_options.min_count - $element.val().length
            })).removeClass('hidden');
          } else {
            jQuery('.enter-more-characters', $wrapper).addClass('hidden');
          }

          $element.data('current_search', '');

          doSetModelAsNotSelected();
        }
      });

      if (jQuery('input.model-value', $wrapper).val().length > 0 ) {
        doSetModelAsSelected();
      }
    }
  }
);

CommonElement.prototype.validateModelSelector = function ()
{
  var apply = this.$element.hasClass('model-input-selector') && this.$element.hasClass('model-required');

  return {
    status:   !apply || 1 === this.$element.data('model-selected'),
    message:  jQuery('.model-not-defined', this.$element.closest('.model-selector')).html(),
    apply:    apply
  };
};
