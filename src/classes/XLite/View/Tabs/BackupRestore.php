<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to Backup/Restore section
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class BackupRestore extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'db_backup';
        $list[] = 'db_restore';

        return $list;
    }

    /**
     * File size limit
     *
     * @return string
     */
    public function getUploadMaxFilesize()
    {
        return ini_get('upload_max_filesize');
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'db_backup' => [
                'weight'   => 100,
                'title'    => static::t('Backup database'),
                'template' => 'db/backup.twig',
            ],
            'db_restore' => [
                'weight'   => 200,
                'title'    => static::t('Restore database'),
                'template' => 'db/restore.twig',
            ],
        ];
    }
}
