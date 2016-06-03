<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View;

/**
 * Page
 *
 * @ListChild (list="center", zone="customer")
 */
class CustomerPage extends \XLite\View\AView
{

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'page';

        return $list;
    }

    /**
     * getDir
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/SimpleCMS/page';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

}
