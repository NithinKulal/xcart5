{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Name condition
 
 * @ListChild (list="qsl.specialoffersbase.special_offers.list.search.conditions", weight="100")
 *}

<li class="condition name">
  <widget class="XLite\View\FormField\Input\Text" fieldName="name" value="{getCondition(#name#):r}" label="{t(#Name#)}" />
</li>