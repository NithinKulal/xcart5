<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Logic\Import;

/**
 * Importer
 */
class Importer extends \XLite\Logic\Import\Importer implements \XLite\Base\IDecorator
{
    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        $list = parent::defineSteps();
        $list[] = 'XLite\Module\CDev\AmazonS3Images\Logic\Import\Step\MoveImages';
        $list[] = 'XLite\Module\CDev\AmazonS3Images\Logic\Import\Step\MoveResizedImages';

        return $list;
    }
}
