/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Listbox javascript functions
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * TODO: Review this function and remove it's obsolete
 */
function normalizeSelect(name)
{
  var tmp = document.getElementById(name);
  if (tmp) {
    tmp.options[tmp.options.length-1] = null;
  }
}

/**
 * Move options from on list to other one
 */
function moveSelect(id, direction)
{
  var left = document.getElementById(id + '-listbox-select-from');
  var right = document.getElementById(id + '-listbox-select-to');

  if (direction) {
    var tmp = left;
    left = right;
    right = tmp;
  }

  if (!left || !right) {
    return false;
  }

  while (right.selectedIndex != -1) {
    left.options[left.options.length] = new Option(right.options[right.selectedIndex].text, right.options[right.selectedIndex].value);
    right.options[right.selectedIndex] = null;
  }

  return true;
}

/**
 * Prepare list 'TO' for saving values
 */
function saveSelects(objects)
{
  if (!objects) {
    return false;
  }

  for (var sel = 0; sel < objects.length; sel++) {

    var id = objects[sel] + '-listbox-select-to';

    if (document.getElementById(id)) {
      if (document.getElementById(objects[sel] + '-store').value == '') {
        for (var x = 0; x < document.getElementById(id).options.length; x++) {
          document.getElementById(objects[sel] + '-store').value += document.getElementById(id).options[x].value + ';';
        }
      }
    }
  }

  return true;
}
