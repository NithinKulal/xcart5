<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\Controller\Admin;

/**
 * Order page controller
 */
class Order extends \XLite\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    /**
     * Assemble volume discount dump surcharge
     *
     * @return array
     */
    protected function assembleDiscountDumpSurcharge()
    {
        return $this->assembleDefaultDumpSurcharge(
            \XLite\Model\Base\Surcharge::TYPE_DISCOUNT,
            \XLite\Module\CDev\VolumeDiscounts\Logic\Order\Modifier\Discount::MODIFIER_CODE,
            '\XLite\Module\CDev\VolumeDiscounts\Logic\Order\Modifier\Discount',
            static::t('Discount')
        );
    }

    /**
     * Get required surcharges
     *
     * @return array
     */
    protected function getRequiredSurcharges()
    {
        return array_merge(
            parent::getRequiredSurcharges(),
            array(\XLite\Module\CDev\VolumeDiscounts\Logic\Order\Modifier\Discount::MODIFIER_CODE)
        );
    }

    /**
     * Add human readable name for DISCOUNT modifier code
     *
     * @return array
     */
    protected static function getFieldHumanReadableNames()
    {
        return array_merge(
            parent::getFieldHumanReadableNames(),
            array(
                \XLite\Module\CDev\VolumeDiscounts\Logic\Order\Modifier\Discount::MODIFIER_CODE  => 'Discount',
            )
        );
    }
}
