<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Core;

/**
 * Mailer
 */
abstract class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * FROM type
     */
    const TYPE_PRODUCTS_RETURN_APPROVED = 'ordersDep';
    const TYPE_PRODUCTS_RETURN_REJECTED = 'ordersDep';

    /**
     * Send mail notification to customer that his products return has been approved
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn $return Canada Post products return model
     *
     * @return void
     */
    public static function sendProductsReturnApproved(\XLite\Module\XC\CanadaPost\Model\ProductsReturn $return)
    {
        if (
            $return->getOrder() 
            && $return->getOrder()->getProfile()
        ) {
            static::register(
                array(
                    'productsReturn' => $return,
                    'notes' => nl2br($return->getAdminNotes(), false)
                )
            );

            static::compose(
                static::TYPE_PRODUCTS_RETURN_APPROVED,
                static::getOrdersDepartmentMail(),
                $return->getOrder()->getProfile()->getLogin(),
                'modules/XC/CanadaPost/return_approved',
                array(),
                true,
                \XLite::CUSTOMER_INTERFACE,
                static::getMailer()->getLanguageCode(\XLite::CUSTOMER_INTERFACE, $return->getOrder()->getProfile()->getLanguage())
            );
        }
    }

    /**
     * Send mail notification to customer that his products return has been rejected
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn $return Canada Post products return model
     *
     * @return void
     */
    public static function sendProductsReturnRejected(\XLite\Module\XC\CanadaPost\Model\ProductsReturn $return)
    {
        if (
            $return->getOrder() 
            && $return->getOrder()->getProfile()
        ) {
            static::register(
                array(
                    'productsReturn' => $return,
                    'notes' => nl2br($return->getAdminNotes(), false)
                )
            );

            static::compose(
                static::TYPE_PRODUCTS_RETURN_REJECTED,
                static::getOrdersDepartmentMail(),
                $return->getOrder()->getProfile()->getLogin(),
                'modules/XC/CanadaPost/return_rejected',
                array(),
                true,
                \XLite::CUSTOMER_INTERFACE,
                static::getMailer()->getLanguageCode(\XLite::CUSTOMER_INTERFACE, $return->getOrder()->getProfile()->getLanguage())
            );
        }
    }
}
