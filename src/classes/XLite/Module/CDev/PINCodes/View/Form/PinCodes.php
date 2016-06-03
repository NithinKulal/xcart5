<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\Form;

/**
 * PinCodes
 *
 */
class PinCodes extends \XLite\View\Form\AForm
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/PINCodes/product/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/PINCodes/product/script.js';

        return $list;
    }
    
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'product';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update_pin_codes';
    }

    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return parent::getDefaultParams() + array('product_id' => \XLite\Core\Request::getInstance()->product_id);
    }
}
