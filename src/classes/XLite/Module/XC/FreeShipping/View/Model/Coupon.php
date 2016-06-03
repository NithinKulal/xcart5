<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\Model;

/**
 * Coupon model form extension
 *
 * @Decorator\Depend("CDev\Coupons")
 */
class Coupon extends \XLite\Module\CDev\Coupons\View\Model\Coupon implements \XLite\Base\IDecorator
{
    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        $this->schemaDefault['value'][self::SCHEMA_DEPENDENCY] = array(
            self::DEPENDENCY_HIDE => array(
                'type' => array(\XLite\Module\CDev\Coupons\Model\Coupon::TYPE_FREESHIP),
            ),
        );

        return $this->getFieldsBySchema($this->schemaDefault);
    }
}
