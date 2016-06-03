<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Controller\Admin;

/**
 * Coupon
 */
class Coupon extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var   array
     */
    protected $param = array('target', 'id');

    /**
     * Coupon id
     *
     * @var   integer
     */
    protected $id;

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage coupons');
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $model = $this->getModelForm()->getModelObject();

        return ($model && $model->getId())
            ? $model->getCode()
            : static::t('Coupon');
    }

    /**
     * Update coupon
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');

        if ($this->getModelForm()->isValid()) {
            $this->setReturnURL(
                \XLite\Core\Converter::buildURL(
                    'promotions',
                    '',
                    array('page' => \XLite\Controller\Admin\Promotions::PAGE_COUPONS)
                )
            );
        }
    }

    /**
     * Returns coupon
     *
     * @return \XLite\Module\CDev\Coupons\Model\Coupon
     */
    protected function getCoupon()
    {
        return $this->getModelForm()->getModelObject();
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\CDev\Coupons\View\Model\Coupon';
    }
}
