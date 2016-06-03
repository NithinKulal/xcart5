<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Product page widgets collection
 */
class ProductPageCollection extends \XLite\View\AWidgetsCollection
{

    /**
     * Widget parameters
     */
    const PARAM_PRODUCT = 'product';


    /**
     * Product modifier types
     *
     * @var array
     */
    protected $productModifierTypes;

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject('Product', $this->getDefaultProduct(), false, '\XLite\Model\Product'),
        );
    }

    /**
     * Register the view classes collection
     *
     * @return array
     */
    protected function defineWidgetsCollection()
    {
        return array(
            '\XLite\View\Price',
            '\XLite\View\Product\Details\Customer\CommonAttributes',
        );
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
        $result = true;

        switch ($name) {
            case '\XLite\View\Price':
                $types = $this->getProductModifierTypes();
                if (empty($types['price'])) {
                    $result = false;
                }
                break;

            case '\XLite\View\Product\Details\Customer\CommonAttributes':
                $types = $this->getProductModifierTypes();
                if (empty($types['weight']) && empty($types['sku'])) {
                    $result = false;
                }
                break;

            default:
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
        if (!isset($this->productModifierTypes)) {
            foreach (\XLite\Model\Attribute::getTypes() as $type => $name) {
                $class = \XLite\Model\Attribute::getAttributeValueClass($type);
                if (is_subclass_of($class, 'XLite\Model\AttributeValue\Multiple')) {
                    $modifierTypes = \XLite\Core\Database::getRepo($class)
                        ->getModifierTypesByProduct($this->getProduct());
                    foreach ($modifierTypes as $k => $v) {
                        if (!isset($this->productModifierTypes[$k])) {
                            $this->productModifierTypes[$k] = $v;

                        } else {
                            $this->productModifierTypes[$k] = $this->productModifierTypes[$k] || $v;
                        }
                    }
                }
            }
        }

        return $this->productModifierTypes;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    /**
     * Get default product
     *
     * @return \XLite\Model\Product
     */
    protected function getDefaultProduct()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')
            ->find(intval(\XLite\Core\Request::getInstance()->product_id));
    }
}
