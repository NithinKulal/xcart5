<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Admin;

/**
 * Save mode tooltips
 */
class SaveModeTooltips extends \XLite\View\Product\Details\Admin\AAdmin
{
    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'product/attributes';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/save_mode_tooltips.twig';
    }
}
