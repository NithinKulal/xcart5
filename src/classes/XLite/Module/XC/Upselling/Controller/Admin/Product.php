<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\Controller\Admin;

/**
 * Product modify
 *
 */
class Product extends \XLite\Controller\Admin\Product implements \XLite\Base\IDecorator
{
    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $pages = parent::getPages();
        if (!$this->isNew()) {
            $pages['upselling_products'] = static::t('Related products');
        }

        return $pages;
    }

    /**
     * The parent product ID definition
     *
     * @return string
     */
    public function getParentProductId()
    {
        return \XLite\Core\Request::getInstance()->product_id ?: \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $tpls = parent::getPageTemplates();

        if (!$this->isNew()) {
            $tpls += array(
                'upselling_products' => 'modules/XC/Upselling/upselling_products.twig',
            );
        }

        return $tpls;
    }
}
