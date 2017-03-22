<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Model;

/**
 * Product selector widget
 */
class ProductSelector extends \XLite\View\FormField\Select\Model\Selector
{
    /**
     * Defines the JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'form_field/model_selector/product/controller.js';

        return $list;
    }

    /**
     * Defines the model specific JS-namespace event
     *
     * @return string
     */
    protected function getDataType()
    {
        return 'product';
    }

    /**
     * Defines the text phrase if no models are found
     *
     * @return string
     */
    protected function getDefaultEmptyPhrase()
    {
        return static::t('No products found');
    }

    /**
     * Defines the text if no model is selected
     *
     * @return string
     */
    protected function getDefaultEmptyModelDefinition()
    {
        return static::t('SKU is not selected');
    }

    /**
     * Defines the URL to request the models
     *
     * @return string
     */
    protected function getDefaultGetter()
    {
        return $this->buildURL('model_product_selector');
    }

    /**
     * Defines the text value of the model
     *
     * @return string
     */
    protected function getTextValue()
    {
        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getValue());

        return $product ? $product->getName() : '';
    }

    /**
     * Defines the name of the text value input
     *
     * @return string
     */
    protected function getTextName()
    {
        return $this->getParam(static::PARAM_NAME) . '_text';
    }

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        $value = parent::prepareRequestData($value);

        if (is_numeric($value)) {
            return \XLite\Core\Database::getRepo('XLite\Model\Product')->find($value);
        }

        return $value;
    }
}
