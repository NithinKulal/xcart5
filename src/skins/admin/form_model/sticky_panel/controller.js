/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('form_model/sticky_panel', ['js/vue/vue'], function (XLiteVue) {

  XLiteVue.component('xlite-sticky-panel', {
    props: ['changed'],
    activate: function (done) {
      done();

      this.timer = null;
      this.$base = jQuery(this.$el);
      this.$panel = this.$base.find('.box').eq(0);
      this.$document = jQuery(window.document);
      this.$window = jQuery(window);

      this.defaultOptions = {
        bottomPadding:       0,
        parentContainerLock: true
      };

      this.parentContainerTop = null;
      this.lastScrollTop = null;
      this.lastHeight = null;

      setTimeout(_.bind(this.processReposition, this), 0);
      // this.processReposition();

    },
    watch: {
      'changed': function (newValue, oldValue) {
        if (newValue !== oldValue) {
          var buttons = jQuery(this.$el).find('button').not('.always-enabled');

          if (newValue) {
            buttons
              .removeClass('disabled')
              .removeProp('disabled');

          } else {
            buttons
              .addClass('disabled')
              .prop('disabled', 'disabled');
          }

        }
      }
    },
    methods: {
      processReposition: function () {
        this.$base.height(this.$panel.outerHeight() + 3);

        this.lastScrollTop = this.$document.scrollTop();
        this.lastHeight = this.$window.height();
        this.panelHeight = this.$base.height();
        this.parentContainerTop = this.$base.parent().offset().top;

        // Assign move operators
        jQuery(window)
          .scroll(_.bind(this.checkRepositionEvent, this))
          .resize(_.bind(this.checkRepositionEvent, this));

        core.bind(
          'stickyPanelReposition',
          _.bind(this.reposition, this)
        );

        this.reposition();
      },
      reposition: function (isResize) {
        var options = this.defaultOptions;

        this.$panel.stop();

        var boxScrollTop = this.$base.offset().top;
        var docScrollTop = this.$document.scrollTop();
        var windowHeight = this.$window.height();
        var ownerWidth   = this.$base.outerWidth();
        var diff = windowHeight - boxScrollTop + docScrollTop - this.$panel.outerHeight() - options.bottomPadding;

        if (0 > diff) {
          if (options.parentContainerLock && this.parentContainerTop > (boxScrollTop + diff)) {
            this.$panel.css({position: 'absolute', top: this.parentContainerTop - boxScrollTop});

          } else if ('fixed' != this.$panel.css('position')) {
            this.$panel.css({
              position:   'fixed',
              top:        windowHeight - this.$panel.outerHeight() - options.bottomPadding,
              width:      ownerWidth
            });
            this.$panel.addClass('sticky');

          } else if (isResize) {
            this.$panel.css({position: 'fixed', top: windowHeight - this.$panel.outerHeight() - options.bottomPadding});
          }

        } else if (this.$panel.css('top') != '0px') {
          this.$panel.css({position: 'absolute', top: 0});
          this.$panel.removeClass('sticky');
        }
      },
      checkRepositionEvent: function () {
        if (this.timer) {
          clearTimeout(this.timer);
          this.timer = null;
        }

        this.timer = setTimeout(
          _.bind(this.checkRepositionEventTick, this),
          0
        );
      },
      checkRepositionEventTick: function () {
        var scrollTop = this.$document.scrollTop();
        var height = this.$window.height();

        if (Math.abs(scrollTop - this.lastScrollTop) > 0 || height != this.lastHeight) {
          var resize = height != this.lastHeight;
          this.lastScrollTop = scrollTop;
          this.lastHeight = height;

          this.reposition(resize);
        }
      }
    }
  });

});
