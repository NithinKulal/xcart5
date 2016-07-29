/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Upgrade note controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Controller
 */
function UpgradeTopBoxController()
{
  this.callSupermethod('constructor', arguments);

  this.base = jQuery(this.pattern).eq(0);

  if (this.base && this.base.length) {
    this.block = new UpgradeTopBox(this.base);
    core.bind('mp_event_check_for_updates', _.bind(this.handleUpdateUpgradeBlock, this));
  }
}

extend(UpgradeTopBoxController, AController);

UpgradeTopBoxController.prototype.name = 'UpgradeTopBoxController';

UpgradeTopBoxController.prototype.pattern = '.upgrade-box';

UpgradeTopBoxController.prototype.block = null;

UpgradeTopBoxController.prototype.handleUpdateUpgradeBlock = function(event, data)
{
  if (data) {

    // Refresh base as this may changed after initialization
    var base = jQuery(this.pattern).eq(0);

    if (data.check_for_updates_data && base.hasClass('invisible')) {
      // Upgrades available: reload block
      this.block.load();

    } else if (!data.check_for_updates_data && !base.hasClass('invisible')) {
      // Upgrades not available: just hide block
      var isBoxVisible = base.hasClass('post-opened') || base.hasClass('opened');
      if (isBoxVisible) {
        base.removeClass('opened').removeClass('post-opened');
        base.addClass('corner-invisible');
      }
      setTimeout(
        function() {
          if (isBoxVisible) {
            // Close box with animation
            base.addClass('closed');
            setTimeout(function() { base.addClass('invisible'); }, 1100);

          } else {
            base.addClass('invisible');
          }

          jQuery('body').removeClass('upgrade-box-visible').removeClass('upgrade-box-hidden');
        },
        1100
      );
    }
  }
}

/**
 * Widget
 */
function UpgradeTopBox(base)
{
  this.callSupermethod('constructor', arguments);
  this.process(base);
  this.bind('local.loaded', _.bind(this.handleLoaded, this))
}

extend(UpgradeTopBox, ALoadable);

UpgradeTopBox.prototype.shadeWidget = false;

UpgradeTopBox.prototype.widgetTarget = 'main';

UpgradeTopBox.prototype.widgetClass = '\\XLite\\View\\UpgradeTopBox';

UpgradeTopBox.prototype.process = function(base)
{
  if (base) {
    var base = jQuery(base);

  } else {
    var base = jQuery(UpgradeTopBoxController.prototype.pattern);
  }

  base.find('a.close').click(
    function() {
      if (base.hasClass('opened') || base.hasClass('post-opened')) {
        base.removeClass('opened').removeClass('post-opened').addClass('closed');
        jQuery('body').removeClass('upgrade-box-visible').addClass('upgrade-box-hidden');
        setTimeout(
          function() {
            jQuery.ajax({
              url: xliteConfig.script + "?target=main&action=set_notifications_as_read&menuType=toplinksMenuReadHash"
            }).done(function() {
              base.addClass('post-closed').removeClass('closed');
            });
          },
          1100
        );

      } else {
        base.removeClass('closed').removeClass('post-closed').addClass('opened');
        jQuery('body').removeClass('upgrade-box-hidden').addClass('upgrade-box-visible');
        setTimeout(
          function() {
            base.addClass('post-opened').removeClass('opened');
          },
          1100
        );
      }

      return false;
    }
  );

  base.find('a.warning').click(
    function() {
      if (base.hasClass('closed') || base.hasClass('post-closed')) {
        base.find('.close').click();
      }

      return false;
    }
  );
}

UpgradeTopBox.prototype.handleLoaded = function(event, state)
{
  this.process();
}

core.autoload(UpgradeTopBoxController);
