/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Connection to marketplace
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Controller
 */
function ConnectToMarketplaceController()
{
  this.callSupermethod('constructor', arguments);

  this.base = jQuery(this.pattern).eq(0);

  if (this.base && this.base.length) {
    // Preload language variable
    core.t('Reloading...');
    // Bind action for event mp_event_get_addons
    core.bind('mp_event_get_addons', _.bind(this.handleUpdateMarketplaceBlock, this));
    // Display error block if response marketplace 'mp_event_get_addons' event did not received in 10 seconds
    setTimeout(this.showErrorBlock, 10000);
  }

  core.trigger('forceMpAction');
}

extend(ConnectToMarketplaceController, AController);

ConnectToMarketplaceController.prototype.pattern = '.marketplace-connection-block';

ConnectToMarketplaceController.prototype.errorBlockVisible = false;

ConnectToMarketplaceController.prototype.handleUpdateMarketplaceBlock = function(event, data)
{
  if (data) {

    if (data.get_addons_data) {
      // Modules available: reload block
      jQuery('.pending-marketplace-connection .box', this.base).html(core.t('Reloading...'));
      setTimeout(function() { location.reload(); }, 1000);

    } else {
      // Modules not available: display error block
      this.showErrorBlock();
    }
  }
}

ConnectToMarketplaceController.prototype.showErrorBlock = function()
{
  if (!this.errorBlockVisible) {
    jQuery('.pending-marketplace-connection', this.base).addClass('invisible');
    jQuery('.marketplace-not-connected', this.base).removeClass('invisible');
  }
}

core.autoload(ConnectToMarketplaceController);
