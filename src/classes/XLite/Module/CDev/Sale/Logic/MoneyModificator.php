<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Logic;

/**
 * Net price modificator
 */
class MoneyModificator extends \XLite\Logic\ALogic
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
        return static::getObject($model) instanceOf \XLite\Model\Product
            && static::getObject($model)->getParticipateSale();
    }

    /**
     * Modify money
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return \XLite\Model\AEntity
     */
    static protected function getObject(\XLite\Model\AEntity $model)
    {
        return $model instanceOf \XLite\Model\Product ? $model : $model->getProduct();
    }
}
