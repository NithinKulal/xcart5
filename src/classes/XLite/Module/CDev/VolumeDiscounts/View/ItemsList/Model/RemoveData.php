<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View\ItemsList\Model;

/**
 * Remove data items list
 */
abstract class RemoveData extends \XLite\View\ItemsList\Model\RemoveData implements \XLite\Base\IDecorator
{
    const TYPE_DISCOUNTS = 'discounts';

    /**
     * Get plain data
     *
     * @return array
     */
    protected function getPlainData()
    {
        return parent::getPlainData() + array(
            static::TYPE_DISCOUNTS => array(
                'name' => static::t('Volume discounts'),
            ),
        );
    }

    /**
     * Build metod name
     *
     * @param \XLite\Model\AEntity $entity  Entity
     * @param string               $pattern Pattern
     *
     * @return string
     */
    protected function buildMetodName(\XLite\Model\AEntity $entity, $pattern)
    {
        return static::TYPE_DISCOUNTS == $entity->getId()
            ? sprintf($pattern, 'Discounts')
            : parent::buildMetodName($entity, $pattern);
    }

    /**
     * Check - allow remove coupons or not
     *
     * @return boolean
     */
    protected function isAllowRemoveDiscounts()
    {
        return 0 < \XLite\Core\Database::getRepo('XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount')->count();
    }

    /**
     * Remove coupons
     *
     * @return integer
     */
    protected function removeDiscounts()
    {
        return $this->removeCommon('XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount');
    }

}
