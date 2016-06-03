{* vim: set ts=2 sw=2 sts=2 et: *}

<a href="{buildUrl(#special_offer#,##,_ARRAY_(#offer_id#^getOfferId()))}" class="special-offer-cell">
  <span class="special-offer-image" IF="hasImage()"><widget class="\XLite\View\Image" image="{getImage()}" maxWidth="{getImageWidth()}" maxHeight="{getImageHeight()}" centerImage=0 verticalAlign="middle" alt="{getImageAlt()}" /></span>
  <span class="special-offer-short-text">{getPromoText():h}</span>
</a>
