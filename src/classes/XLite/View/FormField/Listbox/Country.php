<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Listbox;

/**
 * Countres listbox widget
 */
class Country extends \XLite\View\FormField\Listbox\AListbox
{
    /**
     * Widget param names
     */
    const PARAM_ALL = 'all';

    /**
     * Display only enabled countries
     *
     * @var boolean
     */
    protected $onlyEnabled = true;

    /**
     * Save current form reference and sections list, and initialize the cache
     *
     * @param array $params Widget params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        if (!empty($params[static::PARAM_ALL])) {
            $this->onlyEnabled = false;
        }

        parent::__construct($params);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_ALL => new \XLite\Model\WidgetParam\TypeBool('All', false),
        );
    }

    /**
     * Get selector default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = $this->onlyEnabled
            ? \XLite\Core\Database::getRepo('XLite\Model\Country')->findAllEnabled()
            : \XLite\Core\Database::getRepo('XLite\Model\Country')->findAllCountries();

        $options = array();

        foreach ($list as $country) {
            $options[$country->getCode()] = $country->getCountry();
        }

        return $options;
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        return parent::getValueContainerClass() . ' country-listbox';
    }
}
