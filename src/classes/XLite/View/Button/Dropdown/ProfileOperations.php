<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Dropdown;

/**
 * Profile operations
 *
 * @ListChild (list="tabs.items", zone="admin", weight="100")
 */
class ProfileOperations extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * @inheritdoc
     */
    public static function getAllowedTargets()
    {
        $targets = parent::getAllowedTargets();

        return array_merge($targets, \XLite\View\Tabs\AdminProfile::getAllowedTargets());
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        $result = false;

        foreach ($this->getAdditionalButtons() as $button) {
            if ($button->isVisible()) {
                $result = true;
                break;
            }
        }

        return $result && parent::isVisible();
    }

    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'operateAdmin' => [
                'class'    => 'XLite\View\Button\LoginAsAdmin',
                'params'   => [],
                'position' => 50,
            ],
            'logout'       => [
                'class'    => 'XLite\View\Button\TerminateProfileSessions',
                'params'   => [],
                'position' => 100,
            ],
            'operate'      => [
                'class'    => 'XLite\View\Button\OperateAsThisUser',
                'params'   => [],
                'position' => 200,
            ],
        ];
    }

    /**
     * Get default label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Profile actions';
    }

    /**
     * @return boolean
     */
    protected function getDefaultUseCaretButton()
    {
        return false;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'profile/profile_operations.css';

        return $list;
    }

    /**
     * Get default CSS class name
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return 'profile-actions always-enabled';
    }
}
