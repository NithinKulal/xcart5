<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\Form;

/**
 * Saved crediit cards form 
 */
class PopupSavedCards extends \XLite\View\Form\AForm
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'order';
    }

    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'recharge';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $params = array();

        if (\XLite::isAdminZone()) {
            $params = array(
                'amount' => \XLite\Core\Request::getInstance()->amount,
                'order_number' => \XLite\Core\Request::getInstance()->order_number
            );    
        };

        return $params;

    }

    /**
     * Check and (if needed) set the return URL parameter
     *
     * @param array &$params Form params
     *
     * @return void
     */
    protected function setReturnURLParam(array &$params)
    {
        parent::setReturnURLParam($params);
        $params[\XLite\Controller\AController::RETURN_URL] = $this->buildFullURL('order', '', array('order_number' => \XLite\Core\Request::getInstance()->order_number));

    }
}
