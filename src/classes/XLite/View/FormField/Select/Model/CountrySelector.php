<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Model;

/**
 * Country selector widget
 */
class CountrySelector extends \XLite\View\FormField\Select\Model\Selector
{
    /**
     * Defines the JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'form_field/model_selector/country/controller.js';

        return $list;
    }

    /**
     * Defines the model specific JS-namespace event
     *
     * @return string
     */
    protected function getDataType()
    {
        return 'country';
    }

    /**
     * Defines the text phrase if no models are found
     *
     * @return string
     */
    protected function getDefaultEmptyPhrase()
    {
        return static::t('No countries found');
    }

    /**
     * Defines the text if no model is selected
     *
     * @return string
     */
    protected function getDefaultEmptyModelDefinition()
    {
        return static::t('Country is not selected');
    }

    /**
     * Defines the URL to request the models
     *
     * @return string
     */
    protected function getDefaultGetter()
    {
        return $this->buildURL('model_country_selector');
    }

    /**
     * Defines the text value of the model
     *
     * @return string
     */
    protected function getTextValue()
    {
        $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($this->getValue());

        return $country ? $country->getCountry() : '';
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
}
