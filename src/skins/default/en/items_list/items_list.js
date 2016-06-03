/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Common list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Main class
function ListsController(base)
{
  ListsController.superclass.constructor.apply(this, arguments);

  if (this.base && this.base.length) {
    this.block = this.getListView();
  }
}

extend(ListsController, AController);

ListsController.prototype.name = 'ItemsListController';

ListsController.prototype.findPattern = '.items-list';

ListsController.prototype.block = null;

ListsController.prototype.selfUpdated = false;

ListsController.prototype.getListView = function()
{
  return new ListView(this.base);
};

// Initialize controller
ListsController.prototype.initialize = function()
{
  var o = this;

  this.base.bind(
    'reload',
    function(event, box) {
      o.bind(box);
    }
  );
};

/**
 * Main widget (listView)
 */

function ListView(base)
{
  this.widgetClass  = core.getCommentedData(base, 'widget_class');
  this.widgetTarget = core.getCommentedData(base, 'widget_target');
  this.widgetParams = core.getCommentedData(base, 'widget_params');
  this.listenToHash = core.getCommentedData(base, 'listenToHash');
  this.listenToHashPrefix = core.getCommentedData(base, 'listenToHashPrefix');

  ListView.superclass.constructor.apply(this, arguments);
}

extend(ListView, ALoadable);

ListView.prototype.shadeWidget = true;

ListView.prototype.sessionCell = null;

ListView.prototype.postprocess = function(isSuccess, initial)
{
  ListView.superclass.postprocess.apply(this, arguments);

  if (isSuccess) {

    var o = this;

    // Register page click handler
    jQuery('.pagination a', this.base).click(
      function() {
        // scroll page to list top
        jQuery('html, body').animate({scrollTop: o.base.offset().top});
        return !o.load({'pageId': core.getValueFromClass(this, 'page')});
      }
    );
    jQuery('input.page-length').validationEngine();

    // Register page count change handler
    jQuery('input.page-length', this.base).change(
      function() {
        core.clearHash('pageId');
        if (!o.isLoading) {
          if(!jQuery(this).validationEngine('validate')) {
            count = parseInt(jQuery(this).val());

            if (isNaN(count)) {
              //TODO We must take it from the previous widget parameters ...
              count = 10;

            } else if (count < 1) {
              count = 1;
            }

            if (count != jQuery(this).val()) {
              jQuery(this).val(count);
            }

            o.load({ 'itemsPerPage': count });
          }
        }
      }
    );

    jQuery('input.page-length', this.base).keypress(
      function(event) {
        if (event.keyCode == 13) {
          jQuery(event.currentTarget).change();
        }
      }
    );
  }
};
