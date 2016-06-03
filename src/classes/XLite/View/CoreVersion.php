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
class CoreVersion extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'top_links/version_notes/parts/core.twig';
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
            && \XLite\Core\Auth::getInstance()->isAdmin();
    }

    /**
     * Alias
     *
     * @return string
     */
    protected function getCurrentCoreVersion()
    {
        return \XLite::getInstance()->getVersion();
    }

    /**
     * Alias
     *
     * @return string
     */
    protected function getEditionName()
    {
        $result = 'Trial';

        $license = \XLite::getXCNLicense();

        if ($license && ($keyData = $license->getKeyData()) && !empty($keyData['editionName'])) {

            if (!is_array($keyData)) {
                $keyData = unserialize($keyData);
            }

            if (isset($keyData['editionName'])) {
                $result = $keyData['editionName'];
            }
        }

        return $result;
    }

}
