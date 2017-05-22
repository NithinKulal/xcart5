<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\View;

/**
 * Migrate images
 *
 * @ListChild (list="crud.modulesettings.footer", zone="admin", weight="100")
 */
class Migrate extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'module';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/AmazonS3Images/migrate.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/AmazonS3Images/migrate.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/AmazonS3Images/migrate.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && 'CDev\\AmazonS3Images' == $this->getModule()->getActualName()
            && \XLite\Module\CDev\AmazonS3Images\Core\S3::getInstance()->isValid();
    }

    /**
     * Get migration process started code
     *
     * @return string
     */
    protected function getMigrateStarted()
    {
        $result = false;

        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState('migrateFromS3');
        if ($state && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->isFinishedEventState('migrateFromS3')) {
            $result = 'migrateFromS3';
        }

        if (!$result) {
            $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState('migrateToS3');
            if ($state && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->isFinishedEventState('migrateToS3')) {
                $result = 'migrateToS3';
            }
        }

        return $result;
    }

    /**
     * Get migrate percent
     *
     * @return integer
     */
    protected function getPercentMigrate()
    {
        $percent = 0;

        $info = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState('migrateFromS3');

        if ($info) {
            $percent = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventStatePercent('migrateFromS3');
        }

        if (!$info || 100 == $percent) {
            $info = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState('migrateToS3');

            if ($info) {
                $percent = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventStatePercent('migrateToS3');
            }
        }

        return $percent;
    }

    // {{{ Migrate from S3

    /**
     * Check - has S3 images or not
     *
     * @return boolean
     */
    protected function hasS3Images()
    {
        $result = true;

        foreach (\XLite\Model\Repo\Base\Image::getManagedRepositories() as $class) {
            if (\XLite\Core\Database::getRepo($class)->hasNoS3Images(true)) {
                $result = false;
                break;
            }
        }

        if ($result) {
            $result = false;
            foreach (\XLite\Model\Repo\Base\Image::getManagedRepositories() as $class) {
                if (\XLite\Core\Database::getRepo($class)->hasS3Images(true)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Check migrate from Amazon S3 form visibility
     *
     * @return boolean
     */
    protected function isMigrateFromS3Visible()
    {
        return !$this->getMigrateStarted() && $this->hasS3Images();
    }

    // }}}

    // {{{ Migrate to S3

    /**
     * Check - has non-S3 images  or not
     *
     * @return boolean
     */
    protected function hasNoS3Images()
    {
        $result = false;

        foreach (\XLite\Model\Repo\Base\Image::getManagedRepositories() as $class) {
            if (\XLite\Core\Database::getRepo($class)->hasNoS3Images(true)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Check migrate to Amazon S3 form visibility
     *
     * @return boolean
     */
    protected function isMigrateToS3Visible()
    {
        return !$this->getMigrateStarted() && $this->hasNoS3Images();
    }

    // }}}
}

