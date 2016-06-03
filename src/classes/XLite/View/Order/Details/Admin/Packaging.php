<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Details\Admin;

/**
 * Order packaging widget
 *
 */
class Packaging extends \XLite\View\AView
{
    /**
     * Widget parameters
     */
    const PARAM_PACKAGES = 'packages';


    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/history/packaging.twig';
    }


    /**
     * Define widget parameters
     *
     * @return array
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PACKAGES => new \XLite\Model\WidgetParam\TypeCollection('Packages', array()),
        );
    }

    /**
     * Get array of packages
     *
     * @return array
     */
    protected function getPackages()
    {
        return $this->getParam(self::PARAM_PACKAGES);
    }
}
