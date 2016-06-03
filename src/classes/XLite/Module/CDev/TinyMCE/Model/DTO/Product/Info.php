<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\TinyMCE\Model\DTO\Product;

/**
 * Product
 */
class Info extends \XLite\Model\DTO\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\Product $dataObject
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($dataObject, $rawData = null)
    {
        parent::populateTo($dataObject, $rawData);

        $dataObject->setBriefDescription($rawData['default']['description']);
        $dataObject->setDescription($rawData['default']['full_description']);
    }
}
