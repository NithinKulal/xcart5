<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Controller\Customer;

/**
 * X-Payment connector iframe 
 *
 */
class XpcIframe extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Get viewer template
     *
     * @return string 
     */
    protected function getViewerTemplate()
    {
        return 'modules/CDev/XPaymentsConnector/checkout/iframe/main.twig';
    }

}
