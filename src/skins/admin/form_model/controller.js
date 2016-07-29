/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('form_model_start', ['js/vue/vue', 'ready'], function (XLiteVue) {
  XLiteVue.start();
});

define('form_model', ['js/vue/vue'], function (XLiteVue) {
  var state = false;

  XLiteVue.component('xlite-form-model', {
    props: ['form', 'original', 'changed'],
    activate: function (done) {
      var self = this;
      setTimeout(function () {
        self.$set('original', JSON.parse(JSON.stringify(self.form)));
        state = true;
      }, 1000);
      done();
    },
    directives: {
      xliteBackendValidator: {
        bind: function () {
          var el = this.el;
          this.vm.$watch(this.expression, function () {
            if (el.parentNode) {
              el.parentNode.removeChild(el);
            }
          })
        }
      }
    },
    methods: {
      isChanged: function (model, event) {
        if (state === false) {
          state = objectHash.sha1(this.form);
          state = null;

          return true;
        }

        if (state === null) {
          state = objectHash.sha1(this.form);

          return false;
        }

        var result = false;
        for (var sectionName in this.original) {
          for (var fieldName in this.original[sectionName]) {
            if (typeof this.original[sectionName][fieldName] == 'object') {
              if (objectHash.sha1(this.form[sectionName][fieldName]) != objectHash.sha1(this.original[sectionName][fieldName])) {
                result = true;
              }
            } else {
              if (this.form[sectionName][fieldName] != this.original[sectionName][fieldName]) {
                result = true;
              }
            }
          }
        }

        result = result && !this.$form.invalid;

        this.changed = result;
        return result;
      },
      onSubmit: function (event) {
        var self = this;
        this.$validate(true, function () {
          if (self.$form.invalid) {
            event.preventDefault()
          }
        })
      }
    }
  });
});
