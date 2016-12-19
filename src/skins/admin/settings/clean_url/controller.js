/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
CleanURLSwitcher = Object.extend({
  constructor: function CleanURLSwitcher(base) {
    this.onFormatChange();

    jQuery('#company-name, #parent-category-path, #object-name, #object-name-in-page-title-order')
        .change(_.bind(this.onFormatChange,this));

    jQuery('#clean-url-flag').change(_.bind(this.onCleanURLFlagChange,this));
  },

  buildTitle: function(options, companyName, categoryPath, titleObjectPart) {
    var title = [];

    if (companyName && options.company) {
      title.push(companyName);
    }

    if (categoryPath && options.category) {
      title.push(categoryPath);
    }

    title.push(titleObjectPart);

    if (options.order == true) {
      title = title.reverse();
    }

    return title.join(options.titleDelimiter)
  },

  onFormatChange: function() {
    var template = _.template("<div class='product'><span><%=productTitle%>: </span><span><%=product%></span></div><div class='category'><span><%=categoryTitle%>: </span><span><%=category%></span></div><div class='static'><span><%=staticTitle%>: </span><span><%=static%></span></div>");
    var helpData = core.getCommentedData('#clean-url-help-data');

    var options = {
      company: jQuery('#company-name').is(':checked'),
      category: jQuery('#parent-category-path').is(':checked'),
      order:  jQuery('#object-name-in-page-title-order').is(':checked'),
      titleDelimiter: helpData.delimiter
    };

    var data = {
      'product':  this.buildTitle(options, helpData.companyNameLabel, helpData.categoryNameLabel, helpData.productNameLabel),
      'category': this.buildTitle(options, helpData.companyNameLabel, helpData.parentCategoryNameLabel, helpData.categoryNameLabel),
      'static':   this.buildTitle(options, helpData.companyNameLabel, '', helpData.staticPageNameLabel),
      'productTitle':   helpData.productTitle,
      'categoryTitle':  helpData.categoryTitle,
      'staticTitle':    helpData.staticTitle
    };

    var htmlContent = template(data);

    var block = jQuery('.general_options-table .cleanurls-format-help');
    if (block.length === 0) {
      block = jQuery("<li class='cleanurls-format-help'></li>");
      block.insertAfter('.general_options-table .cleanurls-pagetitleformat');
    }

    block.html(htmlContent);
  },

  onCleanURLFlagChange: function (event)
  {
    event.stopImmediatePropagation();

    core.get(
      URLHandler.buildURL({
        target: 'settings',
        page: 'Environment',
        action: 'switch_clean_url'
      })
    ).done(function (data) {
      if (false === data['Success']) {
        jQuery('.clean-url-setting-error-msg').html(data['Error']['msg']);
        jQuery('.clean-url-setting-error-body').html(data['Error']['body']);
        jQuery('#clean-url-flag').prop('checked', false);
      }else{
        jQuery('.clean-url-setting-error-msg').html('');
        jQuery('.clean-url-setting-error-body').html('');
        core.trigger('message', {
          type: 'info',
          message: data['NewState'] ? core.t('Clean URLs are enabled') : core.t('Clean URLs are disabled')
        });
      }
      //'Clean URLs functionality may not be enabled. More info');
    });

    return false;
  }

});

core.autoload(CleanURLSwitcher);
