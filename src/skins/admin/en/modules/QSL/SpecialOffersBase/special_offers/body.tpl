{* vim: set ts=2 sw=2 sts=2 et: *}

{*
 * Special offers page template
 *}
 
<widget template="common/dialog.tpl" body="modules/QSL/SpecialOffersBase/special_offers/create.tpl" />
<widget IF="isSearchVisible()" template="common/dialog.tpl" body="modules/QSL/SpecialOffersBase/special_offers/search.tpl" />
<widget template="common/dialog.tpl" body="modules/QSL/SpecialOffersBase/special_offers/list.tpl" />