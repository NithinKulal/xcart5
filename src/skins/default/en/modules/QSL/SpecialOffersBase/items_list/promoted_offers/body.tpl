{* vim: set ts=2 sw=2 sts=2 et: *}

<div class="promoted-special-offers {getFingerprint()}">
  <ul class="special-offers special-offers-columns-{getColumnsCount()}">
    {foreach:getRows(),rowIndex,row}{foreach:row,columnIndex,offer}<li class="{getRowCSSClass(rowIndex)} {getColumnCSSClass(rowIndex,columnIndex)}" style="{getItemInlineStyle()}">
      <widget class="{getWidgetClassname()}" offer="{offer}" row="{rowIndex}" column="{columnIndex}" />
    </li>{end:}{end:}
  </ul>
</div>