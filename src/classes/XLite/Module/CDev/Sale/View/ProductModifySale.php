<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * Product options list
 */
class ProductModifySale extends \XLite\View\FormField\Input\AInput
{
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return static::FIELD_TYPE_HIDDEN;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/Sale/product_modify_sale/js/script.js';

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

        $list[] = 'modules/CDev/Sale/product_modify_sale/css/style.css';

        return $list;
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Sale/product_modify_sale';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'body.twig';
    }
}
