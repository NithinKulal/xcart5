<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View;

/**
 * Product page widgets collection
 */
class ProductPageCollection extends \XLite\View\ProductPageCollection implements \XLite\Base\IDecorator
{
    /**
     * Register the view classes collection
     *
     * @return array
     */
    protected function defineWidgetsCollection()
    {
        $widgets = parent::defineWidgetsCollection();

        if ($this->getProduct()->hasVariants()) {
            $widgets = array_merge(
                $widgets,
                array(
                    '\XLite\View\Product\Details\Customer\CommonAttributes',
                    '\XLite\View\Product\Details\Customer\Stock',
                    '\XLite\View\Product\Details\Customer\Quantity',
                    '\XLite\View\Product\Details\Customer\AddButton',
                    '\XLite\View\Product\Details\Customer\EditableAttributes',
                )
            );
        }

        return array_unique($widgets);
    }

    /**
     * Check - allowed display subwidget or not
     *
     * @param string $name Widget class name
     *
     * @return boolean
     */
    protected function isAllowedWidget($name)
    {
        $result = parent::isAllowedWidget($name);

        if ($result && $this->getProduct()->hasVariants()) {
            switch ($name) {
                case '\XLite\View\Product\Details\Customer\Quantity':
                case '\XLite\View\Product\Details\Customer\Stock':
                case '\XLite\View\Product\Details\Customer\AddButton':
                    $types = $this->getProductModifierTypes();
                    if (empty($types['quantity'])) {
                        $result = false;
                    }
                    break;

                case '\XLite\View\Product\Details\Customer\CommonAttributes':
                    $types = $this->getProductModifierTypes();
                    if (empty($types['weight']) && empty($types['sku'])) {
                        $result = false;
                    }
                    break;

                case '\XLite\View\Product\Details\Customer\EditableAttributes':
                    $types = $this->getProductModifierTypes();
                    if (empty($types['weight']) && empty($types['price'])) {
                        $result = false;
                    }
                    break;


                default:
            }
        }

        return $result;
    }

    /**
     * Get product modifier types
     *
     * @return array
     */
    protected function getProductModifierTypes()
    {
        $additional = null;
        if (!isset($this->productModifierTypes) && $this->getProduct()->hasVariants()) {
            $additional = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')
                ->getModifierTypesByProduct($this->getProduct());
        }

        $result = parent::getProductModifierTypes();

        if (isset($additional)) {

            foreach ($additional as $key => $value) {
                $result[$key] = isset($result[$key])
                    ? $result[$key] || $value
                    : $value;
            }

            if (!$result['price'] && $additional['price']) {
                $result['price'] = true;
            }
            if (!$result['weight'] && $additional['weight']) {
                $result['weight'] = true;
            }

            $this->productModifierTypes = $result;
        }

        return $result;
    }
}
