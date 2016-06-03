<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Login page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Login extends \XLite\View\AView
{
    /**
     * Time left to unlock
     *
     * @var integer
     */
    protected $timeLeftToUnlock;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        $result[] = 'login';

        return $result;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'login/script.js';

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
        $list[] = 'unauthorized/style.less';
        $list[] = 'login/style.less';

        return $list;
    }

    /**
     * Check - login is locked or not
     *
     * @return integer
     */
    protected function isLocked()
    {
        return 0 < $this->getTimeLeftToUnlock();
    }

    /**
     * Return time left to unlock
     *
     * @return integer
     */
    protected function getTimeLeftToUnlock()
    {
        if (!isset($this->timeLeftToUnlock)) {
            $this->timeLeftToUnlock = \XLite\Core\Session::getInstance()->dateOfLockLogin
                ? \XLite\Core\Session::getInstance()->dateOfLockLogin + \XLite\Core\Auth::TIME_OF_LOCK_LOGIN - \XLite\Core\Converter::time()
                : 0;
        }

        return $this->timeLeftToUnlock;
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'login';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }
}
