<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\Model\DTO\Product;

/**
 * Product
 */
class Info extends \XLite\Model\DTO\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        parent::init($object);

        $this->default->is_customer_attachments_available = $object->getIsCustomerAttachmentsAvailable();
        $this->default->is_customer_attachments_required = $object->getIsCustomerAttachmentsRequired();
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $object->setIsCustomerAttachmentsAvailable((boolean) $this->default->is_customer_attachments_available);
        $object->setIsCustomerAttachmentsRequired((boolean) $this->default->is_customer_attachments_required);
    }
}
