<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\CustomerAttachments\Controller\Customer;

/**
 * Decorate ACustomer controller class
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * Period of deleting requests
     */
    const CUSTOMERS_PERIOD = 500;

    /**
     * Handles the request
     *
     * @return void
     */
    public function handleRequest()
    {
        parent::handleRequest();

        $rand = rand(1, static::CUSTOMERS_PERIOD);
        if ($rand === 1) {
            $models = \XLite\Core\Database::getRepo('\XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment')
                ->findBy(array('orderItem' => null));

            if (!empty($models)) {
                \XLite\Core\Database::getRepo('\XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment')
                    ->deleteInBatch($models, true);
            }
        }
    }
} 
