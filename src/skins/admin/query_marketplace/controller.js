/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Query marketplace widget controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function QueryMarketplace()
{
  core.bind('forceMpAction', _.bind(this.forceMpAction, this));

  QueryMarketplace.superclass.constructor.apply(this, arguments);

  this.queryMarketplace();
}

extend(QueryMarketplace, ALoadable);

QueryMarketplace.prototype.pattern = '#queryMarketplace';

QueryMarketplace.prototype.maxAttepmts = 1;

QueryMarketplace.prototype.completed = false;

QueryMarketplace.prototype.forceMpActions = false;

QueryMarketplace.prototype.forceMpAction = function(event, state)
{
  this.forceMpActions = true;

  if (this.completed) {
    this.queryMarketplace();
  }
}

QueryMarketplace.prototype.isNeedQuery = function(base)
{
  return (this.forceMpActions || 0 < core.getCommentedData(base, 'hasPendingActions').length)
    && 0 < base.length
    && !base.hasClass('active')
    && !base.hasClass('processed');
}

QueryMarketplace.prototype.queryMarketplace = function()
{
  var base = jQuery(this.pattern);

  if (this.isNeedQuery(base)) {
    this.scheduleQuery(base);

  } else {
    this.completed = true;
  }
}

QueryMarketplace.prototype.scheduleQuery = function(base)
{
  var obj = this;

  setTimeout(
    function() {
      base.addClass('active').removeClass('error');
      var suffix = '';
      if (core.getCommentedData(base, 'parentTarget').length) {
        suffix = '&parentTarget=' + core.getCommentedData(base, 'parentTarget');
      }

      jQuery.ajax({
          url: xliteConfig.script + "?target=marketplace&action=update" + suffix,
          async: true,
          dataType: 'json',
          cache: false,
          global: false
        }
      ).done(function(data, textStatus) {
          base.addClass('processed');
          obj.processSuccess(data);
        }
      ).fail(function(xhr, textStatus, error) {
          base.addClass('error');
          this.maxAttepmts -= 1;
          if (0 < this.maxAttepmts) {
            setTimeout(this.queryMarketplace, 5000);
          }
        }
      ).always(function() {
          base.removeClass('active');
          this.completed = true;
        }
      );
    },
    1000
  );
}

QueryMarketplace.prototype.processSuccess = function(data)
{
  if (data && 0 < data.actions.length) {
    for (i = 0; i < data.actions.length; i++) {
      core.trigger('mp_event_' + data.actions[i], data);
    }
  }
}

core.autoload(QueryMarketplace);
