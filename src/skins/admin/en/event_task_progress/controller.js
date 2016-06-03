/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Event task progress bar controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function EventTaskProgress() {
  var self = this;
  // Non blocking event driver
  jQuery('.progress-bar.noblocking').each(
    function () {

      var eventName = jQuery(this).data('event');
      var bar = jQuery('.bar', this);

      core.bind(
        'eventTaskRun',
        function (event, data) {
          return self.initializeNextStep(event, data, bar, eventName);
        }
      );

      bar.progressbar();
      self.initializeNextStep(null, {percent: bar.data('percent')}, bar, eventName);
    }
  );

  // Blocking event driver
  jQuery('.progress-bar.blocking').each(
    function () {

      var bar = jQuery('.bar', this);

      bar.progressbar();
      bar.progressbar('value', bar.data('percent'));

      self.updateProgressBar();
    }
  );

}

EventTaskProgress.prototype.initializeNextStep = function(event, data, bar, eventName, attempt)
  {
    var self = this;
    var percent = 0;
    var oldData = {
      event: event,
      data:  data,
      bar:   bar,
      eventName: eventName,
      attempt: "undefined" ===  typeof(attempt) ? 0 : attempt
    };

    if (data && 'undefined' !== typeof(data.percent)) {
      bar.progressbar('value', data.percent);
      bar.attr('title', data.percent + '%')
      bar.trigger('changePercent', data);
      percent = data.percent;
    }

    if (100 > percent) {
      core.post(
        URLHandler.buildURL({'target': 'event_task', 'action': 'run', 'event': eventName}),
        function(xhr, status, data, valid) {},
        {},
        {
          timeout: 600000,
          success: function (xhr, textStatus) {
          },
          error: function (xhr, textStatus, errorThrown) {
            if (oldData.attempt < 10) {
              setTimeout(function () {
                self.initializeNextStep(oldData.event, oldData.data, oldData.bar, oldData.eventName, oldData.attempt + 1);
              }, 2000);
            } else {
              core.showError('Event task runner internal error');
            }
          }}
      );

    } else {
      if (data.error) {
        bar.trigger('error');
      }
      bar.trigger('complete');
    }
  };

EventTaskProgress.prototype.updateProgressBar = function()
  {
    var timer;
    var timerTTL = 10000;
    var o = this;
    var bar = jQuery('.bar', this);
    var eventName = jQuery(this).data('event');

    core.get(
      URLHandler.buildURL({'target': 'event_task', 'action': 'touch', 'event': eventName}),
      function(xhr, status, data) {
        if (xhr.readyState != 4 || xhr.status != 200) {
          core.showError('Event task touch procedure internal error');

        } else {
          data = jQuery.parseJSON(data);
          if (data && 'undefined' != typeof(data.percent)) {
            bar.progressbar('value', data.percent);
            bar.attr('title', data.percent + '%')
            bar.trigger('changePercent', data);
            if (100 > data.percent) {
              timer = setTimeout(function () { return o.updateProgressBar(); }, timerTTL);

            } else {

              if (data.error) {
                bar.trigger('error');
              }

              bar.trigger('complete');
            }

          } else {
            core.showError('Event task touch procedure internal error');
          }
        }
      },
      {},
      {timeout: 10000}
    );
  };

EventTaskProgress.prototype.eventTaskInitialized = false;

core.autoload(EventTaskProgress);
