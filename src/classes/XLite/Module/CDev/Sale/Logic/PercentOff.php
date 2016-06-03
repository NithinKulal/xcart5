<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Logic;

/**
 * Net price modificator: percent off
 */
class PercentOff extends \XLite\Module\CDev\Sale\Logic\MoneyModificator
{
    /**
     * Check modificator - apply or not
     *
     * @param \XLite\Model\AEntity $model     Model
     * @param string               $property  Model's property
     * @param array                $behaviors Behaviors
     * @param string               $purpose   Purpose
     *
     * @return boolean
     */
    static public function isApply(\XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        return parent::isApply($model, $property, $behaviors, $purpose)
            && \XLite\Module\CDev\Sale\Model\Product::SALE_DISCOUNT_TYPE_PERCENT == static::getObject($model)->getDiscountType();
    }

    /**
     * Modify money
     *
     * @param float                $value     Value
     * @param \XLite\Model\AEntity $model     Model
     * @param string               $property  Model's property
     * @param array                $behaviors Behaviors
     * @param string               $purpose   Purpose
     *
     * @return void
     */
    static public function modifyMoney($value, \XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        $result = $value * (1 - static::getObject($model)->getSalePriceValue() / 100);

        $currency = \XLite::getInstance()->getCurrency();

        if ($currency) {
            $result = \XLite::getInstance()->getCurrency()->roundValue($result);
        }

        return $result;
    }
}
