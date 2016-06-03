<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View;

/**
 * Abstarct widget
 */
abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if (!\XLite::isAdminZone()) {
            $list[] = 'modules/CDev/Paypal/style.css';
            $list[] = array(
                'file'  => 'modules/CDev/Paypal/style.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            );
        }

        return $list;
    }

    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (!\XLite::isAdminZone()
            && \XLite\Module\CDev\Paypal\Main::isExpressCheckoutEnabled()
            && \XLite\Module\CDev\Paypal\Main::isInContextCheckoutAvailable()
        ) {
            $list[] = array(
                'url' => 'https://www.paypalobjects.com/api/checkout.js',
            );
            $list[] = 'modules/CDev/Paypal/button/in_context.js';
        } else {
            $list[] = 'modules/CDev/Paypal/button/default.js';
        }

        return $list;
    }
}
