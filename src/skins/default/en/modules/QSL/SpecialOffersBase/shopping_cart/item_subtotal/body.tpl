{* vim: set ts=2 sw=2 sts=2 et: *}

<td class="item-subtotal">
    <div class="subtotal" IF="!hasSurcharges()">
        <widget class="XLite\View\Surcharge" surcharge="{item.getDisplayTotal()}" currency="{getCurrency()}" />
    </div>
    <div class="subtotal subtotal-with-surcharges" IF="hasSurcharges()">
        <div>
            <del><widget class="XLite\View\Surcharge" surcharge="{getSubtotal()}" currency="{getCurrency()}" /></del>
        </div>
        <div>
            <span class="modified-subtotal" IF="isFreeItem()">{t(#Free#)}!</span>
            <span class="modified-subtotal" IF="!isFreeItem()"><widget class="XLite\View\Surcharge" surcharge="{getTotal()}" currency="{getCurrency()}" /></span>
            <div class="including-modifiers" style="display: none;">
                <table class="including-modifiers">
                    <tr FOREACH="getSurcharges(),surcharge">
                        <td class="name">{surcharge.label}:&nbsp;</td>
                        <td class="value"><widget class="XLite\View\Surcharge" surcharge="{surcharge.value}" currency="{getCurrency()}" /></td>
                    </tr>
                </table>
            </div>
            <list name="cart.item.specialoffer.surcharges" item="{item}" />
        </div>
    </div>
    <list name="cart.item.actions" item="{item}" />
</td>