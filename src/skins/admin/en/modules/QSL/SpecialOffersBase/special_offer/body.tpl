{* vim: set ts=2 sw=2 sts=2 et: *}

{*
 * Special offer page template
 *}
 
 {if:isOfferTypeEnabled()}
<widget class="{getPageWidgetClass()}" useBodyTemplate="1" />
{else:}
    <div class="error-message">{#This offer type requires a module that is disabled or uninstalled at the moment.#}</div>
{end:}
