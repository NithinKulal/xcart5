{* vim: set ts=2 sw=2 sts=2 et: *}

{if:hasActiveOfferTypes()}
<widget class="\XLite\Module\QSL\SpecialOffersBase\View\Form\ItemsList\SpecialOffer\Create" name="create" />
  <ul class="special-offer-create-params">
    {displayViewListContent(#qsl.specialoffersbase.special_offers.list.create#)}
  </ul>
<widget name="create" end />
{else:}
    <widget template="modules/QSL/SpecialOffersBase/special_offers/special_offer_modules.tpl" />
{end:}