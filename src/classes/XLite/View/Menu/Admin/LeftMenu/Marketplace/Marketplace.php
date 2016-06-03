<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu\Marketplace;

/**
 * Marketplace node
 */
class Marketplace extends \XLite\View\Menu\Admin\LeftMenu\ANodeNotification
{
    /**
     * Check if content is updated
     *
     * @return boolean
     */
    public function isUpdated()
    {
        return false;
    }

    // {{{ View helpers

    /**
     * Returns node style class
     *
     * @return array
     */
    protected function getNodeStyleClasses()
    {
        $list = parent::getNodeStyleClasses();
        $list[] = 'marketplace';

        return $list;
    }


    /**
     * Returns icon
     *
     * @return string
     */
    protected function getIcon()
    {
        return $this->getSVGImage('images/marketplace_circle.svg');
    }

    /**
     * Returns header url
     *
     * @return string
     */
    protected function getHeaderUrl()
    {
        return $this->buildURL('addons_list_marketplace', '', array('landing' => true));
    }

    /**
     * Returns header
     *
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Marketplace');
    }

    // }}}
}
