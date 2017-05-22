<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Controller\Admin;

/**
 * Amazon S3 migrate 
 */
class S3Migrate extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Migrate to Amazon S3 
     * 
     * @return void
     */
    protected function doActionMigrateToS3()
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->initializeEventState('migrateToS3');
        \XLite\Core\EventTask::migrateToS3();
    }

    /**
     * Migrate from Amazon S3
     *
     * @return void
     */
    protected function doActionMigrateFromS3()
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->initializeEventState('migrateFromS3');
        \XLite\Core\EventTask::migrateFromS3();
    }
}

