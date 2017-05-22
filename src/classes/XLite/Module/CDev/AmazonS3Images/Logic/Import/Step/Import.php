<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Logic\Import\Step;

/**
 * Import step
 */
class Import extends \XLite\Logic\Import\Step\Import implements \XLite\Base\IDecorator
{
    /**
     * Initialize import step
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        // Set up needMigration flag for all images
        foreach (\XLite\Model\Repo\Base\Image::getManagedRepositories() as $entityClass) {
            \XLite\Core\Database::getRepo($entityClass)->updateNeedMigration(false);
        }
    }
}
