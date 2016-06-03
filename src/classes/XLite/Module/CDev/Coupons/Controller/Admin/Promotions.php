<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Controller\Admin;

/**
 * Coupons
 */
abstract class Promotions extends \XLite\Controller\Admin\Promotions implements \XLite\Base\IDecorator
{
    /**
     * Page key
     */
    const PAGE_COUPONS = 'coupons';

    /**
     * Get pages static
     *
     * @return array
     */
    public static function getPagesStatic()
    {
        $list = parent::getPagesStatic();
        $list[static::PAGE_COUPONS] = array(
            'name' => static::t('Coupons'),
            'tpl'  => 'modules/CDev/Coupons/coupons/body.twig',
            'permission' => 'manage coupons',
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
            || (static::PAGE_COUPONS === $this->getPage()
                && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage coupons')
            );
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionCouponsUpdate()
    {
        $list = new \XLite\Module\CDev\Coupons\View\ItemsList\Coupons();
        $list->processQuick();
    }
}
