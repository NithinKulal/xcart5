<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Mobile header
 *
 * @ListChild (list="layout.header.mobile", weight="100")
 */
class MobileHeader extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/header/mobile.header.twig';
    }

    /**
     * Check block visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return 'checkout' != $this->getTarget()
            || $this->isCheckoutAvailable();
    }

    /**
     * Should customer zone have language selector
     *
     * @return boolean
     */
    public function isNeedLanguageDropDown()
    {
        return 1 < \XLite\Core\Database::getRepo('XLite\Model\Language')->countBy(
            array(
                'enabled'   => true,
                'added'     => true
            )
        );
    }
}
