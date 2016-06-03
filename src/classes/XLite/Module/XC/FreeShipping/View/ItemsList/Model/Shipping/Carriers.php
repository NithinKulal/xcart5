<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\ItemsList\Model\Shipping;

/**
 * Shipping carriers list
 */
class Carriers extends \XLite\View\ItemsList\Model\Shipping\Carriers implements \XLite\Base\IDecorator
{
    /**
     * Disable removing special methods
     *
     * @param \XLite\Model\AEntity $entity Shipping method object
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        /** @var \XLite\Model\Shipping\Method $entity */
        return parent::isAllowEntityRemove($entity) && !$entity->getFree() && !$this->isFixedFeeMethod($entity);
    }

    /**
     * Disable removing special methods
     *
     * @param \XLite\Model\AEntity $entity Shipping method object
     *
     * @return boolean
     */
    protected function isAllowEntitySwitch(\XLite\Model\AEntity $entity)
    {
        /** @var \XLite\Model\Shipping\Method $entity */
        return parent::isAllowEntitySwitch($entity) && !$entity->getFree() && !$this->isFixedFeeMethod($entity);
    }

    /**
     * Return true if method is 'Freight fixed fee'
     *
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return boolean
     */
    protected function isFixedFeeMethod(\XLite\Model\Shipping\Method $method)
    {
        return \XLite\Model\Shipping\Method::METHOD_TYPE_FIXED_FEE === $method->getCode()
            && 'offline' === $method->getProcessor();
    }

    /**
     * Add right actions
     *
     * @return array
     */
    protected function getRightActions()
    {
        return array_merge(
            parent::getRightActions(),
            array(
                'modules/XC/FreeShipping/free_shipping_tooltip.twig',
                'modules/XC/FreeShipping/shipping_freight_tooltip.twig'
            )
        );
    }

    /**
     * Add left actions
     *
     * @return array
     */
    protected function getLeftActions()
    {
        return array_merge(
            parent::getLeftActions(),
            array(
                'modules/XC/FreeShipping/free_shipping_tooltip.twig',
                'modules/XC/FreeShipping/shipping_freight_tooltip.twig'
            )
        );
    }

    /**
     * Check if the column template is used for widget displaying
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isTemplateColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        $result = parent::isTemplateColumnVisible($column, $entity);

        if ($result
            && 'handlingFee' === $column[static::COLUMN_CODE]
            && 'offline' === $entity->getProcessor()
            && (\XLite\Model\Shipping\Method::METHOD_TYPE_FIXED_FEE === $entity->getCode()
                || $entity->getFree())
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Check if the simple class is used for widget displaying
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isClassColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        $result = parent::isClassColumnVisible($column, $entity);

        if ($result
            && 'handlingFee' === $column[static::COLUMN_CODE]
            && 'offline' === $entity->getProcessor()
            && (\XLite\Model\Shipping\Method::METHOD_TYPE_FIXED_FEE === $entity->getCode()
                || $entity->getFree())
        ) {
            $result = false;
        }

        return $result;
    }
}
