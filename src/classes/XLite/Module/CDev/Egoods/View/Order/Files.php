<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View\Order;

/**
 * User files 
 */
class Files extends \XLite\View\AView
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if ($this->getOrdersWithFiles()) {
            $list[] = 'modules/CDev/Egoods/files_page.css';
            $list[] = 'browse_server/icons.css';
        }

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Egoods/files_page.twig';
    }
}

