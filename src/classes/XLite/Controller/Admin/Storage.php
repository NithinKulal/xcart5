<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Storage
 */
class Storage extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Storage
     *
     * @var \XLite\Model\Base\Storage
     */
    protected $storage;

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && ('download' != $this->getAction() || $this->getStorage());
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || $this->checkStoragePermissions();
    }

    /**
     * Check storage permissions
     * Return true if current user can download files of this storage
     *
     * @return boolean
     */
    protected function checkStoragePermissions()
    {
        $result = false;

        $permissions = $this->getStorage() ? $this->getStorage()->getAdminPermissions() : array();

        foreach ($permissions as $perm) {
            if (\XLite\Core\Auth::getInstance()->isPermissionAllowed($perm)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

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
     * Download
     *
     * @return void
     */
    protected function doActionDownload()
    {
        $this->silent = true;
        header('Content-Type: ' . $this->getStorage()->getMime());
        header('Content-Size: ' . $this->getStorage()->getSize());
        header('Content-Disposition: attachment; filename="' . addslashes($this->getStorage()->getFileName()) . '";');
        $this->readStorage($this->getStorage());
    }

    /**
     * Get storage
     *
     * @return \XLite\Model\Base\Storage
     */
    protected function getStorage()
    {
        if (
            !isset($this->storage)
            || !is_object($this->storage)
            || !($this->storage instanceof \XLite\Model\Base\Storage)
        ) {
            $class = \XLite\Core\Request::getInstance()->storage;
            if (\XLite\Core\Operator::isClassExists($class)) {
                $id = \XLite\Core\Request::getInstance()->id;
                $this->storage = \XLite\Core\Database::getRepo($class)->find($id);
                if (!$this->storage->isFileExists()) {
                    $this->storage = null;
                }
            }
        }

        return $this->storage;
    }

    /**
     * Read storage
     *
     * @param \XLite\Model\Base\Storage $storage Storage
     *
     * @return void
     */
    protected function readStorage(\XLite\Model\Base\Storage $storage)
    {
        $storage->readOutput();
    }
}
