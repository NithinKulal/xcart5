<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\UserPermissions\View\FormField;

/**
 * Permissions selector
 */
class Permissions extends \XLite\View\FormField\Select\Tags\ATags
{
    /**
     * Root permission
     *
     * @var \XLite\Model\Role\Permission
     */
    protected $root;

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/UserPermissions/role/permissions.js';

        return $list;
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Role\Permission')->findByEnabled(true) as $perm) {
            $section = $perm->getSection();
            if (!isset($list[$section])) {
                $list[$section] = array(
                    'label'   => $section,
                    'options' => array(),
                );
            }

            $list[$section]['options'][$perm->getId()] = $perm->getPublicName();
        }

        return $list;
    }

    /**
     * Get option attributes
     *
     * @param mixed $value Value
     * @param mixed $text  Text
     *
     * @return array
     */
    protected function getOptionAttributes($value, $text)
    {
        $list = parent::getOptionAttributes($value, $text);

        if ($value == $this->getRootPermission()->getId()) {
            $list['data-isRoot'] = '1';
        }

        return $list;
    }

    /**
     * Get root permission
     *
     * @return void
     */
    protected function getRootPermission()
    {
        if (!isset($this->root)) {
            $this->root = \XLite\Core\Database::getRepo('XLite\Model\Role\Permission')
                ->findOneBy(array('code' => \XLite\Model\Role\Permission::ROOT_ACCESS));
        }

        return $this->root;
    }
}
