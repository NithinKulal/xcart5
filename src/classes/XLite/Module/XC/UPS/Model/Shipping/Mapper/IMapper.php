<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UPS\Model\Shipping\Mapper;

interface IMapper
{
    /**
     * Set input data
     *
     * @param mixed  $inputData
     * @param string $key       Additional data key OPTIONAL
     *
     * @return void
     */
    public function setInputData($inputData, $key = 'default');

    /**
     * Get mapped data
     *
     * @return mixed
     */
    public function getMapped();
}
