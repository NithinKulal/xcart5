<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Payment transaction controller
 */
class PaymentTransaction extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'id');

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $id = intval(\XLite\Core\Request::getInstance()->id);
        $model = $id
            ? \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->find($id)
            : null;

        return ($model && $model->getId())
            ? $model->getName()
            : static::t('Transaction');
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Payment\Transaction';
    }

    /**
     * Check if current page is accessible for current x-cart license
     *
     * @return boolean
     */
    protected function checkLicense()
    {
        return !\XLite::isFreeLicense();
    }
}
