/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Items list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Items list controller
 */
function TableItemsList(cell, URLParams, URLAJAXParams)
{
  TableItemsList.superclass.constructor.apply(this, arguments);
}

extend(TableItemsList, ItemsList);

TableItemsList.prototype.form = null;

TableItemsList.prototype.newLinesIndex = 0;

// Set a param and send the request
TableItemsList.prototype.process = function(paramName, paramValue)
{
  var form = this.container.parents('form').get(0);

  var result = true;

  if (!form || !form.commonController.isChanged(true)) {
    result = ItemsList.prototype.process.apply(this, arguments);
  }

  return result;
};

// Place new list content
TableItemsList.prototype.placeNewContent = function(content)
{
  TableItemsList.superclass.placeNewContent.apply(this, arguments);
  var form = this.container.parents('form').get(0);
  if (form && form.commonController) {
    form.commonController.bindElements();
    jQuery(form).trigger('change');
  }

  core.trigger('stickyPanelReposition');
};

// Pager listener
TableItemsList.prototype.listeners.pager = function(handler)
{
  jQuery('.table-pager .input input', handler.container).change(
    function() {
      return !handler.process('pageId', this.value - 1);
    }
  );

  jQuery('.table-pager a', handler.container).click(
    function() {
      jQuery('html, body').animate({scrollTop: handler.container.offset().top});

      return !(jQuery(this).hasClass('disabled') || handler.process('pageId', jQuery(this).data('pageid')));
    }
  );

};

// Item per page input listener
TableItemsList.prototype.listeners.pagesCount = function(handler)
{
  jQuery('select.page-length', handler.container).change(
    function() {
      return !handler.process('itemsPerPage', this.options[this.selectedIndex].value);
    }
  );
};

// Form listener
TableItemsList.prototype.listeners.form = function(handler)
{
  var form = handler.container.parents('form').eq(0);

  if (form.get(0)) {
    form.get(0).commonController.submitOnlyChanged = false;
  }

  form.bind(
    'state-changed',
    function () {
      handler.processFormChanged(jQuery(this));
    }
  );
  form.bind(
    'state-initial',
    function () {
      handler.processFormUndo(jQuery(this));
    }
  );

};

// Process form and form's elements after form changed
TableItemsList.prototype.processFormChanged = function(form)
{
  this.container.find('.table-pager .input input, .table-pager .page-length').each(
    function () {
      jQuery(this).prop('disabled', 'disabled');
      this.setAttribute('disabled', 'disabled');
    }
  );

  this.markPagerAsDisabled();
};

TableItemsList.prototype.markPagerAsDisabled = function()
{
  var pagerBlock = this.container.find('.table-pager');

  if (pagerBlock.length > 0) {
    assignShadeOverlay(pagerBlock).css('z-index', 3);
    pagerBlock.find('a').addClass('disabled').removeClass('enabled');
  };
}

TableItemsList.prototype.markPagerAsEnabled = function()
{
  var pagerBlock = this.container.find('.table-pager');

  if (pagerBlock.length > 0) {
    unassignShadeOverlay(pagerBlock);
    pagerBlock.find('a').removeClass('disabled').addClass('enabled');
  };
}


// Process form and form's elements after form cancel all changes
TableItemsList.prototype.processFormUndo = function(form)
{
  this.container.find('.table-pager .input input, .table-pager .page-length').removeProp('disabled');
  this.markPagerAsEnabled();
};

// Inline creation button listener
TableItemsList.prototype.listeners.createButton = function(handler)
{
  jQuery('tbody.create :input', handler.container)
    .addClass('no-validate');

  jQuery('button.create-inline', handler.container)
    .removeProp('onclick')
    .click(
      function (event) {

        event.stopPropagation();

        var box = jQuery('tbody.create', handler.container);
        var length = box.find('.line').length;
        handler.newLinesIndex++;
        var idx = handler.newLinesIndex;
        var line = box.find('.create-tpl').clone(true);
        line
          .show()
          .removeClass('create-tpl')
          .addClass('create-line')
          .addClass('line')
          .find(':input')
          .unbind()
          .removeClass('no-validate')
          .each(
            function () {
              if (this.id) {
                this.id = this.id.replace(/-0-/, '-n' + idx + '-');
              }
              this.name = this.name.replace(/\[0\]/, '[' + (-1 * idx) + ']');
            }
          );

        box.append(line);
        _.defer(function(){
          jQuery(':input', line).eq(0).focus();
        });

        var form = box.parents('form').get(0);
        if (form) {
          form.commonController.bindElements();
        }

        jQuery('table.list', handler.container).removeClass('list-no-items');
        jQuery('.no-items', handler.container).hide();
        jQuery('.sticky-panel').css('display', '').height(jQuery('.sticky-panel').find('.box').eq(0).height());
        jQuery('.additional-panel').removeClass('hidden').show();

        if (2 == box.children('tr').length) {
          var rightAction = jQuery('tbody.lines tr td.actions.right', handler.container).eq(0);
          if (rightAction.length) {
            line.find('td.actions.right').width(rightAction.width())
          }
        }

        line.parents('.items-list').get(0).itemsListController.triggerVent('newLineCreated', {idx: idx, line: line});

        core.trigger('itemListNewItemCreated', {idx: idx, line: line});

        core.trigger('stickyPanelReposition');

        handler.triggerVent('line.new.add');

        return false;
      }
    );
};

// Selector actions
TableItemsList.prototype.listeners.selector = function(handler)
{
  jQuery('.actions div.selector', handler.container).click(
    function () {
      var input = jQuery('input', this).get(0);
      if (input) {
        input.checked = !input.checked;
        jQuery('input', this).change();
      }
    }
  );

  jQuery('.actions input.selector', handler.container).click(
    function (event) {
      event.stopPropagation();
    }
  );

  jQuery('.actions input.selector', handler.container).change(
    _.bind(
      function (event) {
        var box = jQuery(event.target).parent('div.selector');

        var oldSelected = 0 < jQuery('.actions div.selector.checked', handler.container).length;

        if (event.target.checked) {
          box.addClass('checked');

        } else {
          box.removeClass('checked');
        }

        var newSelected = 0 < jQuery('.actions div.selector.checked', handler.container).length;

        if (oldSelected != newSelected) {
          if (newSelected) {
            this.triggerVent('selector.checked', {widget: handler});

          } else {
            this.triggerVent('selector.unchecked', {widget: handler});
          }
        }

        return true;
      },
      handler
    )
  );

  jQuery('.actions div.default-value-checkbox input', handler.container).click(
    function (event) {
      jQuery('.actions div.default-value-checkbox', handler.container).removeClass('checked');
      jQuery(this).parent().parent().addClass('checked');
      event.stopPropagation();
    }
  );

  jQuery('.actions div.default-value-checkbox', handler.container).click(
    function () {
      jQuery(this).find('input[type=radio]').click();
    }
  );

  jQuery('input.selectAll', handler.container).click(
    function () {
      var box = jQuery('.actions', handler.container);
      var checked = jQuery(this).prop('checked');
      var last = null;
      box.find('input.selector').each(
        function (index, elem) {
          if (checked != jQuery(elem).prop('checked')) {
            jQuery(elem).prop('checked', checked);
            last = elem;
          }
        }
      );

      if (last) {
        jQuery(last).prop('checked', !checked);
        jQuery(last).click();
      }

      if (checked) {
        box.find('div.selector', handler.container).addClass('checked');
        handler.triggerVent('selector.massChecked', { widget: handler, 'checked': checked });

      } else {
        box.find('div.selector', handler.container).removeClass('checked');
        handler.triggerVent('selector.massUnchecked', { widget: handler, 'checked': checked });
      }
    }
  );
};

// Position changed
TableItemsList.prototype.listeners.positionChanged = function(handler)
{
  jQuery('tbody.lines', handler.container).bind(
    'positionChange',
    function () {
      var i = 0;
      var length = jQuery(this).find('.lines').length;
      jQuery(this).find('.lines').each(
        function () {
          var tr = jQuery(this);

          if (0 == i) {
            tr.addClass('first');
          } else {
            tr.removeClass('first');
          }

          if (length - 1 == i) {
            tr.addClass('last');
          } else {
            tr.removeClass('last');
          }

          if (0 == (i + 1) % 2) {
            tr.addClass('even');
          } else {
            tr.removeClass('even');
          }
        }
      );
    }
  );
};

// Head sort
TableItemsList.prototype.listeners.headSort = function(handler)
{
  jQuery('thead th a.sort', handler.container).click(
    function() {
      return jQuery(this).hasClass('current-sort')
        ? !handler.process('sortOrder', 'asc' == jQuery(this).data('direction') ? 'desc' : 'asc')
        : !handler.process('sortBy', jQuery(this).data('sort'));
    }
  );
};

// Head search
TableItemsList.prototype.listeners.headSearch = function(handler)
{
  jQuery('tbody.head-search input,tbody.head-search select', handler.container).change(
    function() {
      var result = false;
      jQuery(this).parents('td').eq(0).find('input,select,textarea').each(
        function () {
          result = handler.setURLParam(this.name, this.value) || result;
        }
      );

      if (result) {
        handler.loadWidget();
      }

      return false;
    }
  );
};

// Fade resize
TableItemsList.prototype.listeners.fadeResize = function(handler)
{
  /*
  var func = function(isResize)
  {
    var cells = jQuery('tbody td.no-wrap .cell', handler.container);
    if (isResize) {

      // Reset width
      cells.each(
        function() {
          jQuery(this).width(jQuery(this).data('initial-width'));
        }
      );
    }

    cells.each(
      function() {

        // Save initial width
        if (!jQuery(this).data('initial-width')) {
          jQuery(this).data('initial-width', jQuery(this).width());
        }

        // Set new width
        var td = jQuery(this).parents('td').eq(0).width();
        if (td > jQuery(this).outerWidth()) {
          jQuery(this).width(td);
        }
      }
    );
  }

  jQuery(window).resize(_.bind(func, this, true));
  func();
  */
};

// Reassign items list controller
TableItemsList.prototype.reassign = function()
{
  new TableItemsList(this.params.cell, this.params.urlparams, this.params.urlajaxparams);
};

// Get event namespace (prefix)
TableItemsList.prototype.getEventNamespace = function()
{
  return 'list.model.table';
}

function TableItemsListQueue()
{
  jQuery('.widget.items-list').each(
    function (index, elem)
    {
      elem = jQuery(elem);
      var cls = elem.data('js-class');
      cls = (cls && typeof(window[cls]) != 'undefined')
        ? window[cls]
        : TableItemsList;

      new cls(elem);
    }
  );
}

core.autoload(TableItemsListQueue);

jQuery().ready(
  function() {
    //jQuery('.items-list').width(jQuery('.items-list').width());

    core.microhandlers.add(
      'ItemsListWidth',
      '.table-pager',
      function (event) {
        jQuery('.items-list').each(function (index, elem) {
          // jQuery('.list', elem).width(jQuery('.table-pager', elem).width() - 2);
        });
      }
    );

  }
);
