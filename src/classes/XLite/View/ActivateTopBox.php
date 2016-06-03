<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Activate top box
 *
 * @ListChild (list="admin.main.page.header", weight="60", zone="admin")
 */
class ActivateTopBox extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'top_links/version_notes/parts/notice.twig';
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->isAdmin()
            && $this->isNoticeActive();
    }

    /**
     * Check if notice should be displayed in the header
     *
     * @return boolean
     */
    protected function isNoticeActive()
    {
        return !\XLite::getXCNLicense();
    }

}
