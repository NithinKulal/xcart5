<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\Order\Details\Admin;

/**
 * Model
 */
class Model extends \XLite\View\Order\Details\Admin\Model implements \XLite\Base\IDecorator
{
    /**
     * Define modifier form field widget
     *
     * @param array $modifier Modifier
     *
     * @return \XLite\View\FormField\Inline\AInline
     */
    protected function defineDcouponModifierWidget(array $modifier)
    {
        return $this->getWidget(
            array(
                'entity'    => $modifier['object'],
                'fieldName' => $modifier['object']->getCode(),
                'name'      => $modifier['object']->getCode(),
                'namespace' => 'modifiersTotals',
            ),
            'XLite\Module\CDev\Coupons\View\FormField\Inline\Input\Hidden\OrderModifierTotal'
        );
    }
}
