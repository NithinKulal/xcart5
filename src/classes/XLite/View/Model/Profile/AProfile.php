<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model\Profile;

/**
 * Profile model widget
 */
abstract class AProfile extends \XLite\View\Model\AModel
{
    /**
     * Return model object to use
     *
     * @return \XLite\Model\Profile
     */
    public function getModelObject()
    {
        $profile = parent::getModelObject();

        // Reset profile if it's not valid
        if (!\XLite\Core\Auth::getInstance()->checkProfile($profile)) {
            $profile = \XLite\Model\CachingFactory::getObject(__METHOD__, '\XLite\Model\Profile');
        }

        return $profile;
    }

    /**
     * getRequestProfileId
     *
     * @return integer|void
     */
    public function getRequestProfileId()
    {
        return \XLite\Core\Request::getInstance()->profile_id;
    }

    /**
     * Return current profile ID
     *
     * @return integer
     */
    public function getProfileId()
    {
        return \XLite\Core\Auth::getInstance()->isOperatingAsUserMode()
            ? \XLite\Core\Auth::getInstance()->getOperatingAs()
            : $this->getRequestProfileId() ?: \XLite\Core\Session::getInstance()->profile_id;
    }


    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Profile
     */
    protected function getDefaultModelObject()
    {
        $obj = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($this->getProfileId());

        if (!isset($obj)) {
            $obj = new \XLite\Model\Profile();
        }

        return $obj;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Profile';
    }

    /**
     * Return text for the "Submit" button
     *
     * @return string
     */
    protected function getSubmitButtonLabel()
    {
        return \XLite\Core\Auth::getInstance()->isLogged() ? 'Update profile' : 'Create';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['submit'] = new \XLite\View\Button\Submit(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL    => $this->getSubmitButtonLabel(),
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
            )
        );

        return $result;
    }
}
