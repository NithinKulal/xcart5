/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Event task progress bar controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var EventTaskProgress = Object.extend({
  constructor: function EventTaskProgress(element) {
    this.$component = element ? element : $(this.pattern);

    if (!this.$component.length || this.$component.data('initialized')) {
      return;
    }

    this.$component.data('initialized', true);
    this.$progress = this.$component.find(this.progressSelector);
    this.$bar = this.$progress.find(this.barSelector);
    this.$message = this.$component.find(this.messageSelector);
    this.$cancelButton = this.$component.find(this.cancelSelector);
    this.initialize();
  },

  pattern: '.event-task-progress',
  progressSelector: '.progress-bar-container',
  messageSelector: '.progress-message',
  cancelSelector: '.progress-cancel',
  barSelector: '.bar',

  eventTaskInitialized: false,
  userCanceling:        false,
  cancelingRequested:   false,

  initialize: function() {
    if (this.$progress.is('.noblocking')) {
      this.initializeNonblocking();
    } else {
      this.initializeBlocking();
    }

    this.$cancelButton.click(_.bind(this.cancelHandler, this));

    core.bind('step-completed', _.bind(this.closeIfCanceling, this));

    // Preload language labels
    core.loadLanguageHash(core.getCommentedData(this.$component));
  },

  closeIfCanceling: function() {
    if (this.userCanceling && !this.cancelingRequested) {
      var form = this.$cancelButton.closest('form').get(0);
      this.$cancelButton.addClass('disabled');

      if (form) {
        form.commonController.enableBackgroundSubmit();
        form.commonController.submitBackground();
        this.$message.html(core.t('Canceled'));
        this.cancelingRequested = true;
      }
    }
  },

  cancelHandler: function(event, data) {
    this.changeMessage(core.t('Canceling'));

    this.userCanceling = true;

    event.preventDefault();

    return false;
  },

  changeMessage: function(message) {
    if (!this.userCanceling) {
      this.$message.html(message);
    }
  },

  triggerError: function(data) {
    this.$bar.addClass('progress-bar-danger');
    this.$progress.removeClass('active');
    this.$message.addClass('progress-message-error');

    if (data.message) {
      this.changeMessage(data.message);
    }

    this.$bar.trigger('error', data);
  },

  triggerComplete: function(data) {
    this.$bar.trigger('complete', data);
  },

  initializeNonblocking: function() {
    var eventName = this.$progress.data('event');

    var self = this;

    core.bind(
      'eventTaskRun',
      function (event, data) {
        return self.initializeNextStep(event, data, eventName);
      }
    );

    this.initializeNextStep(null, {percent: this.$bar.data('percent')}, eventName);
  },

  initializeBlocking: function() {
    this.changeProgress({ percent: this.$bar.data('percent') });

    this.updateProgressBar();
  },

  initializeNextStep: function(event, data, eventName, attempt) {
    var self = this;
    var percent = 0;
    var oldData = {
      event: event,
      data:  data,
      eventName: eventName,
      attempt: "undefined" ===  typeof(attempt) ? 0 : attempt
    };

    if (data && 'undefined' !== typeof(data.percent)) {
      this.changeProgress(data);
      percent = data.percent;
    }

    if (percent < 100 && !this.userCanceling) {
      this.runEventTask(eventName, oldData);

    } else {
      if (data.error || this.userCanceling) {
        self.triggerError(data);
      }
      self.triggerComplete(data);
    }
  },

  runEventTask: function(eventName, oldData) {
    var self = this;

    core.post(
      this.getEndpoint('run', eventName),
      null,
      {},
      {
        timeout: 600000,
        success: function (xhr, textStatus) {
        },
        error: function (xhr, textStatus, errorThrown) {
          if (oldData.attempt < 10) {
            setTimeout(function () {
              self.initializeNextStep(oldData.event, oldData.data, oldData.eventName, oldData.attempt + 1);
            }, 2000);
          } else {
            self.triggerError({message: 'Event task runner internal error'});
          }
        }}
    ).always(function() {
      core.trigger('step-completed')
    });
  },

  updateProgressBar: function()
  {
    var timer;
    var timerTTL = 10000;
    var self = this;
    var eventName = this.$progress.data('event');

    core.get(
      this.getEndpoint('touch', eventName),
      function(xhr, status, data) {
        if (xhr.readyState != 4 || xhr.status != 200) {
          self.triggerError({message: 'Event task touch procedure internal error'});

        } else {
          data = jQuery.parseJSON(data);
          if (data && 'undefined' != typeof(data.percent)) {
            self.changeProgress(data);

            if (100 > data.percent) {
              timer = setTimeout(
                _.bind(function () { 
                  return this.updateProgressBar(); 
                }, self), timerTTL);
            } else {

              if (data.error) {
                self.triggerError(data);
              }
              self.triggerComplete(data);
            }

          } else {
            self.triggerError({message: 'Event task touch procedure internal error'});
          }
        }
      },
      {},
      {timeout: 10000}
    );
  },

  changeProgress: function(data) {
    this.$bar.attr('title', data.percent + '%');
    this.$bar.css('width', data.percent + '%');

    if (data && 'undefined' !== typeof(data.message)) {
      this.changeMessage(data.message);
    }

    this.$bar.trigger('changePercent', data);
  },

  getEndpoint: function(action, eventName) {
    return URLHandler.buildURL({
      'target': 'event_task',
      'action': action,
      'event': eventName
    });
  },

});

core.autoload(EventTaskProgress);
