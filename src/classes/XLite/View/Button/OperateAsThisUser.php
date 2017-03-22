<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * 'Operate as this user' button widget
 */
class OperateAsThisUser extends \XLite\View\Button\Link
{
    /**
     * Get default CSS class name
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return 'action operate-as-this-user always-enabled';
    }

    /**
     * Get default label
     * todo: move translation here
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Operate as user';
    }

    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL()
    {
        return $this->buildURL('profile', 'operateAs', array(
            'profile_id' => $this->getProfile()->getProfileId()
        ));
    }

    /**
     * Get profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->find(
            \XLite\Core\Request::getInstance()->profile_id
        );
    }

    /**
     * Return true if button is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->isProfileAllowed();
    }

    /**
     * Return true if profile meets conditions
     *
     * @return boolean
     */
    protected function isProfileAllowed()
    {
        return $this->getProfile()
            && $this->getProfile()->isPersistent()
            && !$this->getProfile()->getAnonymous()
            && !$this->getProfile()->isAdmin();
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_BLANK]->setValue(true);
    }
}
