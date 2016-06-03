<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product;

/**
 * Attributes
 *
 * @ListChild (list="admin.product.variants", zone="admin", weight="10")
 */
class Attributes extends \XLite\Module\XC\ProductVariants\View\Product\AProduct
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/script.js';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/attributes';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getMultipleAttributes();
    }

    /**
     * Return attribute title
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     *
     * @return integer
     */
    protected function getAttributeCount(\XLite\Model\Attribute $attribute)
    {
        return count($attribute->getAttributeValue($this->getProduct()));
    }

    /**
     * Return attribute title
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     *
     * @return string
     */
    protected function getAttributeTitle(\XLite\Model\Attribute $attribute)
    {
        return static::t(
            '{{count}} options',
            array(
                'count' => $this->getAttributeCount($attribute)
            )
        );

    }

    /**
     * Attribute is checked flag
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     *
     * @return boolean
     */
    protected function isChecked(\XLite\Model\Attribute $attribute)
    {
        return in_array($attribute->getId(), $this->getVariantsAttributeIds());
    }

    /**
     * Return block style
     *
     * @return string
     */
    protected function getBlockStyle()
    {
        $style = parent::getBlockStyle() . ' attributes';

        if ($this->isAllowVaraintAdd()) {
            $style .= ' checked';
        }

        if ($this->getVariantsAttributes()) {
            $style .= ' hidden';
        }

        return $style;
    }
}
