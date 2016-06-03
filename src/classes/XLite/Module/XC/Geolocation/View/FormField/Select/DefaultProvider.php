<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\View\FormField\Select;

/**
 * Catalog extraction type selector
 */
class DefaultProvider extends \XLite\View\FormField\Select\Regular
{
    protected $providers;

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        if (!$this->providers) {
            $classes = \XLite\Module\XC\Geolocation\Logic\Geolocation::getInstance()->getProviders();
            $providers = array();
            if ($classes) {
                foreach ($classes as $class) {
                    $provider = new $class;
                    $providers[$class] = $provider->getProviderName();
                }
            }
            $this->providers = $providers;
        }
        return $this->providers;
    }
}
