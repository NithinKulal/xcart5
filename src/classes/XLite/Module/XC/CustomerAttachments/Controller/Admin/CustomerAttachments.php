<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\Controller\Admin;

/**
 * Customer attachments admin controller
 */
class CustomerAttachments extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('download'));
    }

    /**
     * Download attachment action
     *
     * @return void
     */
    protected function doActionDownload()
    {
        $request = \XLite\Core\Request::getInstance();
        if ($request->attachment_id) {
            $attachment = \XLite\Core\Database::getRepo('\XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment')
                ->find($request->attachment_id);

            $path = $attachment->getStoragePath();
            $name = basename($path);
            header('Content-Type: ' . $attachment->getMime());
            header('Content-Disposition: attachment; filename="' . $name . '"; modification-date="' . date('r') . ';');
            header('Content-Length: ' . filesize($path));

            readfile($path);

            exit (0);
        }
    }
}