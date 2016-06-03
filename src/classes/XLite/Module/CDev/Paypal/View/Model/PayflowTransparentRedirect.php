<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Model;

/**
 * PayflowTransparentRedirect
 */
class PayflowTransparentRedirect extends \XLite\Module\CDev\Paypal\View\Model\ASettings
{
    /**
     * Save current form reference and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     */
    public function __construct(array $params = array(), array $sections = array())
    {
        parent::__construct($params, $sections);

        unset($this->schemaAdditional['buyNowEnabled']);
    }
}
