<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Admin;

/**
 * Product attributes
 */
class Attributes extends \XLite\View\Product\Details\AAttributes
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'product/attribute/style.css';
        $list[] = 'product/attributes/style.css';

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'product/attributes/script.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'product/attributes/body.twig';
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
        $data = parent::getAttributesList($countOnly);

        if ($countOnly) {
            $result = $data;

        } else {
            $result = array();
            foreach ($data as $attribute) {
                $result[$attribute->getId()] = array(
                    'name' => $this->getWidget(
                        $this->getAttributeNameWidgetParams($attribute),
                        '\XLite\View\FormField\Inline\Input\Text'
                    ),
                    'value' => $this->getWidget(
                        $this->getAttributeValueWidgetParams($attribute),
                        $attribute::getWidgetClass($attribute->getType())
                    ),
                    'entity' => $attribute,
                );
            }
        }

        return $result;
    }

    /**
     * Get list of parameters for attribute name widget
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     *
     * @return array
     */
    protected function getAttributeNameWidgetParams($attribute)
    {
        return array(
            'fieldName'   => 'name',
            'entity'      => $attribute,
            'fieldParams' => array('required' => true),
        );
    }

    /**
     * Get list of parameters for attribute value widget
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     *
     * @return array
     */
    protected function getAttributeValueWidgetParams($attribute)
    {
        return array(
            'attribute' => $attribute,
        );
    }

    /**
     * Return true if attributes can be removed
     *
     * @return boolean
     */
    protected function isRemovable()
    {
        return true;
    }

    /**
     * Return true if new attributes can be added
     *
     * @return boolean
     */
    protected function canAddAttributes()
    {
        return true;
    }

    /**
     * Get 'remove' text
     *
     * @return string
     */
    protected function getPemoveText()
    {
        return $this->getPersonalOnly()
            ? static::t('Remove')
            : static::t('Removing this attribute will affect all the products. Leave this blank to hide this option for the product.');
    }
}
