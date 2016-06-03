<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\ExternalSDK;

/**
 * Google+ SDK loader
 */
class GooglePlus extends \XLite\View\ExternalSDK\AExternalSDK
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoSocial/sdk/gplus.twig';
    }
}

