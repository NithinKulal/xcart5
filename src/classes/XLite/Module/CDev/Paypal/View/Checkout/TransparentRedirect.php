<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Checkout;

/**
 * Transparent redirect widget
 */
class TransparentRedirect extends \XLite\View\AView
{
    protected $token;
    protected $paramList;

    protected $tokenGenerated = false;

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/transparent_redirect/transparent_redirect.twig';
    }
}
