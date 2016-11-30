/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/eCommerceCoreEvent', ['googleAnalytics/event', 'js/underscore'], function (Event, _) {
  var requireEC = _.once(function () {
    ga('require', 'ec');

    ga('set', '&cu', GACore.instance.getSetting('currency'));
  });

  eCommerceCoreEvent = Event.extend({
    namespace: 'Ecommerce',

    constructor: function () {
      eCommerceCoreEvent.superclass.constructor.apply(this, arguments);

      requireEC();
    },

    getActions: function (type, context) {
      if (!jQuery(context).length) {
        context = document;
      }

      var actionsOnPage = _.map(
          jQuery('*[data-ga-ec-action]', context),
          function (actionEl) {
            return jQuery(actionEl).data('ga-ec-action');
          }
      );

      return _.filter(actionsOnPage, function (action) {
        return action['ga-type'] === type;
      });
    },
  });

  eCommerceCoreEvent.instance = new eCommerceCoreEvent();

  return eCommerceCoreEvent;
});