/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('form_model/type/clean_url_type', ['js/vue/vue', 'form_model'], function (XLiteVue) {

  Inputmask.extendAliases({
    CleanUrl : {
      alias: 'Regex',
      onBeforeMask: function (value, opts) {
        var result = value;

        if (result.length) {
          result = result.replace(new RegExp('^.*/'), '');
          result = result.replace(new RegExp('\\' + opts.extension + '$'), '');
        }

        return result;
      }
    }
  });
  
  XLiteVue.component('xlite-form-model', {
    methods: {
      getCleanURLResult: function () {
        return this.cleanUrl.cleanUrlTemplate.replace('#PLACEHOLDER#', '<span class="editable">' + this.$get(this.cleanUrl.model).clean_url + '</span>')
      },
      isCleanURLChanged: function () {
        var savedValue = this.cleanUrl.cleanUrlSavedValue === true ? true : this.cleanUrl.cleanUrlSavedValue.replace(new RegExp('\\' + this.cleanUrl.cleanUrlExtension + '$'), '');

        return savedValue === true || this.$get(this.cleanUrl.model).clean_url !== savedValue;
      },
      isCleanUrlAutogenerate: function () {
        return this.$get(this.cleanUrl.model).autogenerate || this.$get(this.cleanUrl.model).clean_url === '';
      }
    },
    directives: {
      xliteCleanUrl: {
        priority: 3000,
        params: ['cleanUrlTemplate', 'cleanUrlSavedValue', 'cleanUrlExtension'],
        bind: function () {
          this.vm.cleanUrl = {
            model: this.expression,
            cleanUrlTemplate: this.params.cleanUrlTemplate,
            cleanUrlSavedValue: this.params.cleanUrlSavedValue,
            cleanUrlExtension: this.params.cleanUrlExtension
          };
        }
      }
    }
  });
});
