<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductComparison\View\ComparisonTable;

/**
 * Attribute list
 */
class AttributeList extends \XLite\Module\XC\ProductComparison\View\ComparisonTable\AComparisonTable
{
    /**
     * Widget param names
     */
    const PARAM_GROUP = 'group';
    const PARAM_CLASSES = 'classes';

    /**
     * Max text length
     */
    const MAX_TEXT_LENGTH = 100;

    /**
     * Get step title
     *
     * @return string
     */
    protected function getTitle()
    {
        return $this->getAttributeGroup()
            ? $this->getAttributeGroup()->getName()
            : null;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_GROUP => new \XLite\Model\WidgetParam\TypeObject(
                'Group', null, false, '\XLite\Model\AttributeGroup'
            ),
            self::PARAM_CLASSES => new \XLite\Model\WidgetParam\TypeCollection(
                'Product classes', array()
            ),
        );
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getAttributesList(true);
    }

    /**
     * Get attribute group
     *
     * @return \XLite\Model\AttributeGroup
     */
    protected function getAttributeGroup()
    {
        return $this->getParam(static::PARAM_GROUP);
    }

    /**
     * Get product classes
     *
     * @return array
     */
    protected function getProductClasses()
    {
        return $this->getParam(static::PARAM_CLASSES);
    }

    /**
     * Get attributes list
     *
     * @param boolean $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getAttributesList($countOnly = false)
    {
        $cnd = new \XLite\Core\CommonCell;

        $cnd->attributeGroup = $this->getAttributeGroup();
        $cnd->product = null;
        if (!$this->getAttributeGroup()) {
            $cnd->productClass = $this->getProductClasses() ?: null;
        }

        return \XLite\Core\Database::getRepo('\XLite\Model\Attribute')->search($cnd, $countOnly);
    }

    /**
     * Get attribute value
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     * @param \XLite\Model\Product   $product   Product
     *
     * @return string
     */
    protected function getAttributeValue(\XLite\Model\Attribute $attribute, \XLite\Model\Product $product)
    {
        $result = '';

        if (
            !$attribute->getProductClass()
            || (
                $product->getProductClass()
                && $product->getProductClass()->getId() == $attribute->getProductClass()->getId()
            )
        ) {
            switch ($attribute->getType()) {
                case $attribute::TYPE_CHECKBOX:
                    $value = $attribute->getAttributeValue($product, true);
                    if (
                        (is_array($value) && (1 < count($value) || $value[0] == static::t('Yes')))
                        || (!is_array($value) && $value == static::t('Yes'))
                    ) {
                        $result = '<img src="'
                            . \XLite\Core\Layout::getInstance()->getResourceWebPath(
                                'images/tick.png',
                                \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
                                \XLite::CUSTOMER_INTERFACE
                            ) . '" alt="" />';

                    } else {
                        $result = '&mdash;';
                    }
                    break;

                case $attribute::TYPE_TEXT:
                    $result = $attribute->getAttributeValue($product, true);
                    if (static::MAX_TEXT_LENGTH < strlen($result)) {
                        $result = substr($result, 0, static::MAX_TEXT_LENGTH)
                            . '<span class="three-dots">...<div>'
                            . $result
                            . '</div></span>';
                    }
                    $result = nl2br($result);
                    break;

                default:
                    $result = implode(
                        $attribute::DELIMITER,
                        $attribute->getAttributeValue($product, true)
                    );
            }
        }

        return $result;
    }

    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/parts/attributes';
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/list.twig';
    }
}
