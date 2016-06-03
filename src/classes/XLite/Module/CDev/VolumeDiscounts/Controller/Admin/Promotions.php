<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\Controller\Admin;

/**
 * Volume discounts page controller (Promotion section)
 */
class Promotions extends \XLite\Controller\Admin\Promotions implements \XLite\Base\IDecorator
{
    /**
     * Page key
     */
    const PAGE_VOLUME_DISCOUNTS = 'volume_discounts';


    /**
     * Get pages static
     *
     * @return array
     */
    public static function getPagesStatic()
    {
        $list = parent::getPagesStatic();
        $list[static::PAGE_VOLUME_DISCOUNTS] = array(
            'name' => static::t('Volume discounts'),
            'tpl'  => 'modules/CDev/VolumeDiscounts/discounts/body.twig',
            'permission' => 'manage volume discounts',
        );

        return $list;
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || (static::PAGE_VOLUME_DISCOUNTS === $this->getPage()
                && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage volume discounts')
            );
    }

    /**
     * Get currency formatted value
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return \XLite::getInstance()->getCurrency()->getCurrencySymbol();
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionVolumeDiscountsUpdate()
    {
        $list = new \XLite\Module\CDev\VolumeDiscounts\View\ItemsList\VolumeDiscounts();
        $list->processQuick();

        // Additional correction to re-define end of subtotal range for each discount
        \XLite\Core\Database::getRepo('XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount')
            ->correctSubtotalRangeEnd();
    }
}
