<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Controller\Admin;

/**
 * Product modify
 *
 */
class Product extends \XLite\Controller\Admin\Product implements \XLite\Base\IDecorator
{
    /**
     * Update pin codes action handler
     *
     * @return void
     */
    public function doActionUpdatePinCodes()
    {
        $product = $this->getProduct();

        $product->setPinCodesEnabled((bool)\XLite\Core\Request::getInstance()->pins_enabled);
        $product->setAutoPinCodes(\XLite\Core\Request::getInstance()->autoPinCodes);

        if (\XLite\Core\Request::getInstance()->delete) {
            foreach (\XLite\Core\Request::getInstance()->delete as $id => $checked) {
                $obj = \XLite\Core\Database::getRepo('XLite\Module\CDev\PINCodes\Model\PinCode')->findOneBy(
                    array(
                        'id' => $id,
                        'product' => $product->getId(),
                        'isSold' => 0
                    )
                );
                if ($obj) {
                    \XLite\Core\Database::getEM()->remove($obj);
                }
            }
        }

        \XLite\Core\Database::getEM()->flush($product);
        if ($product->hasManualPinCodes()) {
            $product->syncAmount();
            $product->setInventoryEnabled(true);
        }
        \XLite\Core\Database::getEM()->flush();

        \XLite\Core\TopMessage::addInfo('PIN codes data have been successfully updated');
    }

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $pages = parent::getPages();
        if (!$this->isNew()) {
            $pages['pin_codes'] = static::t('PIN codes');
        }

        return $pages;
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
                'pin_codes' => 'modules/CDev/PINCodes/product/pin_codes.twig',
            );
        }

        return $tpls;
    }
}
