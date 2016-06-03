<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Model\DTO\Product;

/**
 * Product
 */
class Info extends \XLite\Model\DTO\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @param mixed|\XLite\Model\Product $data
     */
    protected function init($data)
    {
        parent::init($data);

        $this->marketing->og_tags_type = (string) (int) $data->getUseCustomOG();
        $this->marketing->og_tags = $data->getOpenGraphMetaTags();

    }

    /**
     * @param \XLite\Model\Product $dataObject
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($dataObject, $rawData = null)
    {
        parent::populateTo($dataObject, $rawData);

        $dataObject->setUseCustomOG((boolean) $this->marketing->og_tags_type);
        $dataObject->setOgMeta((string) $rawData['marketing']['og_tags']);
    }
}
