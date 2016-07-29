<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\Settings;

/**
 * Order statuses mapping rules 
 */
class MapRules extends \XLite\Module\CDev\XPaymentsConnector\View\Settings\ASettings
{
    /**
     * List of tabs/pages where this setting should be displayed
     *
     * @return boolean
     */
    public function getPages()
    {
        return array(\XLite\Module\CDev\XPaymentsConnector\Core\Settings::PAGE_MAP_RULES);
    }
}
