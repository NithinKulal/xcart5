<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

/**
 * ThemeTweaker controller
 */
class NotificationEditor extends \XLite\Controller\Admin\AAdmin
{
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->params = array_merge($this->params, ['templatesDirectory', 'interface']);
    }

    // public function doNoAction()
    // {
    //     \XLite\Core\Session::getInstance()->getLanguage()->setCode('ru');
    //     \XLite\Core\Layout::getInstance()->setMailSkin(\XLite::CUSTOMER_INTERFACE);
    //     var_dump(\XLite\Core\Layout::getInstance()->getSkinPaths(\XLite::MAIL_INTERFACE));
    //     die;
    // }

    protected function doActionChangeOrderId()
    {
        $orderNumber = \XLite\Core\Request::getInstance()->order_number;
        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->findOneByOrderNumber($orderNumber);

        if ($order) {
            \XLite\Core\TmpVars::getInstance()->themeTweakerDumpOrderId = $order->getOrderId();
        } else {
            \XLite\Core\TopMessage::addWarning('Order not found');
        }

        $this->setReturnURL($this->getURL());
    }
}
