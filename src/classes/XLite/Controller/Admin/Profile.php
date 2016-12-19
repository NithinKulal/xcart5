<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Profile management controller
 */
class Profile extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters (to generate correct URL in getURL() method)
     *
     * @var array
     */
    protected $params = array('target', 'profile_id');

    /**
     * Return value for the "register" mode param
     *
     * @return string
     */
    public static function getRegisterMode()
    {
        return 'register';
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return ($this->isRegisterMode())
            ? static::t('Create profile')
            : static::t('Edit profile');
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        $profile = $this->getProfile();

        $allowedForCurrentUser = \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage users');
        if ( $profile && $profile->isAdmin() && !\XLite\Core\Auth::getInstance()->isPermissionAllowed('manage admins')) {
            $allowedForCurrentUser = false;
        }

        return parent::checkACL()
            || $allowedForCurrentUser
            || $profile && $profile->getProfileId() == \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && $this->isOrigProfile();
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getModelForm()->getModelObject();
    }

    /**
     * The "mode" parameter used to determine if we create new or modify existing profile
     *
     * @return boolean
     */
    public function isRegisterMode()
    {
        return self::getRegisterMode() === \XLite\Core\Request::getInstance()->mode;
    }

    /**
     * Alias
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return $this->getModelForm()->getModelObject() ?: new \XLite\Model\Profile();
    }


    /**
     * Return true if profile is not related with any order (i.e. it's an original profile)
     *
     * @return boolean
     */
    protected function isOrigProfile()
    {
        return !($this->getProfile()->getOrder());
    }

    /**
     * Class name for the \XLite\View\Model\ form
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return '\XLite\View\Model\Profile\AdminMain';
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('finishOperateAs'));
    }

    /**
     * Modify profile action
     *
     * @return void
     */
    protected function doActionModify()
    {
        $this->getModelForm()->performAction('modify');
    }

    /**
     * actionPostprocessModify
     *
     * @return void
     */
    protected function actionPostprocessModify()
    {
        $params = array();

        if ($this->getModelForm()->isRegisterMode()) {

            // New profile is registered
            if ($this->isActionError()) {

                // Return back to register page
                $params = array('mode' => self::getRegisterMode());

            } else {

                // Send notification
                \XLite\Core\Mailer::sendProfileCreated(
                    $this->getProfile(),
                    \XLite\Core\Request::getInstance()->password
                );

                // Return to the created profile page
                $params = array('profile_id' => $this->getProfile()->getProfileId());
            }

        } else {
            // Existing profile is updated

            $password = null;

            // Check if user's password was changed
            if (!empty(\XLite\Core\Request::getInstance()->password)) {
                $password = \XLite\Core\Request::getInstance()->password;
            }

            \XLite\Core\Mailer::sendProfileUpdated($this->getProfile(), $password);

            // Get profile ID from modified profile model
            $profileId = $this->getProfile()->getProfileId();

            // Return to the profile page
            $params = array('profile_id' => $profileId);

            if (\XLite\Model\Profile::STATUS_DISABLED == $this->getProfile()->getStatus()) {
                // Clear user session if user profile has been disabled
                \XLite\Core\Session::getInstance()->clearUserSession($profileId);
            }
        }

        if (!empty($params)) {
            $this->setReturnURL($this->buildURL('profile', '', $params));
        }
    }

    /**
     * Delete profile action
     *
     * @return void
     */
    protected function doActionDelete()
    {
        $this->getModelForm()->performAction('delete');

        // Send notification to the user
        \XLite\Core\Mailer::sendProfileDeleted($this->getProfile()->getLogin());

        $this->setReturnURL($this->buildURL('profile_list'));
    }

    /**
     * Register anonymous profile
     * 
     * @return void
     */
    protected function doActionRegisterAsNew()
    {
        $result = false;
        $profile = $this->getModelForm()->getModelObject();

        if (
            $profile
            && $profile->isPersistent()
            && $profile->getAnonymous()
            && !$profile->getOrder()
            && !\XLite\Core\Database::getRepo('XLite\Model\Profile')->findUserWithSameLogin($profile)
        ) {
            $profile->setAnonymous(false);
            $password = \XLite\Core\Database::getRepo('XLite\Model\Profile')->generatePassword();
            $profile->setPassword(\XLite\Core\Auth::encryptPassword($password));

            $result = $profile->update();
        }

        if ($result) {

            // Send notification to the user
            \XLite\Core\Mailer::sendRegisterAnonymousCustomer($profile, $password);

            \XLite\Core\TopMessage::addInfo('The profile has been registered. The password has been sent to the user\'s email address');
        }
    }

    /**
     * Merge anonymous profile with registered 
     * 
     * @return void
     */
    protected function doActionMergeWithRegistered()
    {
        $result = false;
        $profile = $this->getModelForm()->getModelObject();

        if (
            $profile
            && $profile->isPersistent()
            && $profile->getAnonymous()
            && !$profile->getOrder()
        ) {
            $same = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findUserWithSameLogin($profile);
            if ($same && !$same->isAdmin()) {
                $same->mergeWithProfile($profile);
                $result = $same->update();
                if ($result) {
                    $profile->delete();
                }
            }
        }

        if ($result) {
            \XLite\Core\TopMessage::addInfo('The profiles have been merged');
            $this->setReturnURL(static::buildURL('profile', '', array('profile_id' => $same->getProfileId())));
        }
    }

    /**
     * Operate as user
     * 
     * @return void
     */
    protected function doActionOperateAs()
    {
        $profile = $this->getModelForm()->getModelObject();

        if (
            $profile
            && !$profile->getAnonymous()
        ) {
            \XLite\Core\Auth::getInstance()->setOperatingAs($profile);

            \XLite\Core\TopMessage::addInfo(
                'You are operating as: user',
                array('user' => $profile->getLogin())
            );
            $this->setReturnURL($this->getShopURL(''));
        }
    }

    /**
     * Operate as user
     *
     * @return void
     */
    protected function doActionTerminateSessions()
    {
        $profile = $this->getModelForm()->getModelObject();

        if ($profile) {
            $profile->logoffSessions(false);
            \XLite\Core\TopMessage::addInfo('Success');
        } else {
            \XLite\Core\TopMessage::addError('Error');
        }

        $this->setReturnURL($this->buildURL('profile', '', ['profile_id' => $profile->getProfileId()]));
    }


    /**
     * Login as admin
     *
     * @return void
     */
    protected function doActionLoginAs()
    {
        $profile = $this->getModelForm()->getModelObject();

        if (
            $profile
            && !$profile->getAnonymous()
            && $profile->isAdmin()
            && !$profile->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
            && (\XLite\Core\Auth::getInstance()->isPermissionAllowed('manage admins')
                || \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS))
        ) {
            \XLite\Core\Auth::getInstance()->loginProfile($profile, false);

            \XLite\Core\TopMessage::addInfo(
                'You are logged in as: user',
                array('user' => $profile->getLogin())
            );
            $this->setReturnURL(
                \XLite::getInstance()->getShopURL(
                    \XLite::getAdminScript()
                )
            );
        }
    }
}
