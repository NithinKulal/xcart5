<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View;

use XLite\Module\CDev\GoogleAnalytics;

/**
 * Abstract widget
 */
abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if  ((GoogleAnalytics\Main::useUniversalAnalytics() && !\XLite::isAdminZone())
            || (\XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled() && \XLite::isAdminZone())
        ) {
            $list[static::RESOURCE_JS][] = 'modules/CDev/GoogleAnalytics/universal/ga-core.js';
            $list[static::RESOURCE_JS][] = 'modules/CDev/GoogleAnalytics/universal/ga-event.js';

            if (\XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()) {
                // eCommerce files
                $list[static::RESOURCE_JS][] = 'modules/CDev/GoogleAnalytics/universal/action/ecommerce/ga-ec-full-refund.js';
                $list[static::RESOURCE_JS][] = 'modules/CDev/GoogleAnalytics/universal/action/ecommerce/ga-ec-purchase.js';
                $list[static::RESOURCE_JS][] = 'modules/CDev/GoogleAnalytics/universal/action/ecommerce/ga-ec-core.js';
                $list[static::RESOURCE_JS][] = 'modules/CDev/GoogleAnalytics/universal/action/ecommerce/ga-ec-change-item.js';
            }
        }

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

        if ($this->isIncludeController()) {
            $list[] = 'modules/CDev/GoogleAnalytics/drupal.js';

        } elseif (\XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()) {

            // eCommerce files
            $list[] = 'modules/CDev/GoogleAnalytics/universal/action/ecommerce/ga-ec-impressions.js';
            $list[] = 'modules/CDev/GoogleAnalytics/universal/action/ecommerce/ga-ec-product-click.js';
            $list[] = 'modules/CDev/GoogleAnalytics/universal/action/ecommerce/ga-ec-product-details-shown.js';

            $list[] = 'modules/CDev/GoogleAnalytics/universal/action/ecommerce/ga-ec-checkout-step.js';
            $list[] = 'modules/CDev/GoogleAnalytics/universal/action/ecommerce/ga-ec-checkout-fastlane.js';
            $list[] = 'modules/CDev/GoogleAnalytics/universal/action/ecommerce/ga-change-shipping.js';
            $list[] = 'modules/CDev/GoogleAnalytics/universal/action/ecommerce/ga-change-payment.js';
        }

        return $list;
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if ('module' == $this->getTarget()) {
            $list[] = 'modules/CDev/GoogleAnalytics/style.css';
        }

        return $list;
    }

    /**
     * Display widget as Standalone-specific
     *
     * @return boolean
     */
    protected function isIncludeController()
    {
        return \XLite\Core\Operator::isClassExists('\XLite\Module\CDev\DrupalConnector\Handler')
            && \XLite\Module\CDev\DrupalConnector\Handler::getInstance()->checkCurrentCMS()
            && !GoogleAnalytics\Main::useUniversalAnalytics()
            && function_exists('googleanalytics_theme');

    }
}
