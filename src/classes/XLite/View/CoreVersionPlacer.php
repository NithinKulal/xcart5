<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Core version
 *
 * @ListChild (list="admin.main.page.header", weight="20", zone="admin")
 */
class CoreVersionPlacer extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'top_links/version_notes/parts/core_placer.twig';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->isLogged()
            && \XLite\Core\Auth::getInstance()->isAdmin()
            && !$this->getWidget(array(), '\XLite\\View\\CoreVersion')->isVisible();
    }

}
