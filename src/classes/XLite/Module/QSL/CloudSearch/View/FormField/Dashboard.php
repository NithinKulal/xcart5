<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\FormField;

use XLite\Model\WidgetParam\TypeSet;

/**
 * String-based
 */
class Dashboard extends \XLite\View\FormField\AFormField
{
    const PARAM_SECTION = 'section';

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/CloudSearch/dashboard_style.css';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/QSL/CloudSearch/dashboard_loader.js';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = 'js/jquery.blockUI.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/CloudSearch/form_field/dashboard.twig';
    }

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return '\XLite\Module\QSL\CloudSearch\View\FormField\Dashboard';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/QSL/CloudSearch/form_field/dashboard.tpl';
    }

    /**
     * Getter for Field-only flag
     *
     * @return boolean
     */
    protected function getDefaultParamFieldOnly()
    {
        return true;
    }

    /*
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_SECTION => new TypeSet('Section', null, false, ['cloud_search', 'cloud_filters']),
        );
    }

    /**
     * Get CloudSearch initialization data to pass to the JS code
     *
     * @return array
     */
    protected function getCloudSearchAdminData()
    {
        return array(
            'admin_self' => \XLite::getAdminScript(),
            'section'    => $this->getParam(self::PARAM_SECTION),
        );
    }
}
