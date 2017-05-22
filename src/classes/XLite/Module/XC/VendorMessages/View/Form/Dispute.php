<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Form;

/**
 * Dispute
 */
class Dispute extends \XLite\View\Form\AForm
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return \XLite::isAdminZone() ? 'order' : 'order_messages';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultAction()
    {
        return 'open_dispute';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultParams()
    {
        $list = parent::getDefaultParams();
        $list['order_number'] = $this->getOrder()->getOrderNumber();
        $list['recipient_id'] = intval(\XLite\Core\Request::getInstance()->recipient_id);
        $list['open_dispute_popup'] = 1;

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' dispute');
    }

}
