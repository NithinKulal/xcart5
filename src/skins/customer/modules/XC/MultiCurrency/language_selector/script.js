/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Language controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Controller
 */

function LanguageController(base)
{
  this.callSupermethod('constructor', arguments);
}

extend(LanguageController, AController);

// Controller name
LanguageController.prototype.name = 'LanguageController';

// Find pattern
LanguageController.prototype.findPattern = '.language-selector';

// Body handler is binded or not
LanguageController.prototype.bodyHandlerBinded = false;

// Initialize controller
LanguageController.prototype.initialize = function()
{
  if (!this.bodyHandlerBinded) {
    jQuery('body').click(_.bind(
        function (event) {
          this.toggleViewMode(false);
        },
        this
    ));

    this.bodyHandlerBinded = true;
  }

  jQuery(this.base).children('ul:first').click(_.bind(
      function(event, box) {
        event.stopPropagation();

        this.toggleViewMode()

        return false;
      },
      this
  ));

  jQuery('.items-list', this.base).click(
      function(event) {
        event.stopPropagation();
      }
  );
};

// Toggle view mode
LanguageController.prototype.toggleViewMode = function(expand)
{
  if (expand !== true && expand !== false) {
    expand = !this.base.hasClass('expanded');
  }

  if (expand) {
    this.base.addClass('expanded').removeClass('collapsed');
  } else {
    this.base.removeClass('expanded').addClass('collapsed');
  }
};

core.autoload(LanguageController);