/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Common button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function setFormAttribute(form, name, value)
{
  form.elements[name].value = value;
}

function setFormAction(form, action)
{
    setFormAttribute('action', action);
}

function submitForm(form, attrs)
{
  jQuery.each(
    attrs,
    function (name, value) {
      var e = form.elements.namedItem(name);
      if (e) {
        e.value = value;
      }
    }
  );

	jQuery(form).submit();
}

function submitFormDefault(form, action)
{
	var attrs = {};
  if (typeof(action) != 'undefined' && action !== null) {
  	attrs['action'] = action;
  }

	submitForm(form, attrs);
}

jQuery(document).ready(function () {
  jQuery('a.disabled').click(function(event){
    event.preventDefault();
  });
});
