<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Re-deploy store button
 */
class ReDeployStore extends \XLite\View\Button\Link
{
    /**
     * JavaScript: this code will be used by default
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        return sprintf('if (confirm(core.t("Are you sure?"))) %s', parent::getDefaultJSCode());
    }

    /**
     * Defines the default location path
     *
     * @return null|string
     */
    protected function getDefaultLocation()
    {
        return $this->buildURL('cache_management', 'rebuild');
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Re-deploy the store';
    }
}
