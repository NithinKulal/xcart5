<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\View;

/**
 * Customer attachment popup widget
 *
 * @ListChild (list="center")
 */
class AttachmentPopup extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    static public function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'customer_attachments';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/CustomerAttachments/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/CustomerAttachments/attachment_popup.twig';
    }
}
