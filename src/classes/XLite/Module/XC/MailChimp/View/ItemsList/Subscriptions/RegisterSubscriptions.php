<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\ItemsList\Subscriptions;

use XLite\Module\XC\MailChimp\Core;

/**
 * MailChimp mail lists
 */
class RegisterSubscriptions extends \XLite\Module\XC\MailChimp\View\ItemsList\Subscriptions\AMailChimpSubscriptions
{
    const PARAM_REGISTER_MODE = 'registerMode';

    /**
     * Get available register modes
     *
     * @return array
     */
    public static function getDefaultRegisterMode()
    {
        return 'all';
    }

    /**
     * Get available register modes
     *
     * @return array
     */
    public static function getRegisterModes()
    {
        return array(
            'all',
            'choice'
        );
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_REGISTER_MODE => new \XLite\Model\WidgetParam\TypeSet(
                'Register mode',
                static::getDefaultRegisterMode(),
                static::getRegisterModes()
            )
        );
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $return = parent::getCSSFiles();

        $return[] = array(
            'file'  => $this->getDir() . '/subscriptions_list.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        if ($this->getParam(static::PARAM_REGISTER_MODE) === 'all') {
            $return[] = array(
                'file'  => $this->getDir() . '/subscriptions_list_all.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            );
        }

        return $return;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getTemplate()
    {
        return $this->getParam(static::PARAM_REGISTER_MODE) === 'all'
            ? $this->getDir() . '/subscriptions_register_all.twig'
            : parent::getTemplate();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/subscriptions_register.twig';
    }

    /**
     * Check if profile is subscribed to the MailChimp list
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list    List
     * @param \XLite\Model\Profile|null                      $profile Profile
     *
     * @return boolean
     */
    protected function checkIfSubscribed(\XLite\Module\XC\MailChimp\Model\MailChimpList $list, $profile)
    {
        return (isset($profile) && !\XLite\Core\Auth::getInstance()->checkProfile($profile))
            ? true
            : $list->isProfileSubscribed($profile);
    }

    /**
     * Check if profile is subscribed to the any MailChimp list
     *
     * @return boolean
     */
    public function isSubscribeToAllChecked()
    {
        $found = false;

        $lists = $this->getData();

        foreach ($lists as $key => $list) {
            if ($this->checkIfSubscribed($list, $this->getProfile())) {
                $found = true;
                break;
            };
        }

        return $found;
    }

    /**
     * Get subscribeToAll field name
     *
     * @return string
     */
    protected function getSubscribeToAllFieldName()
    {
        return Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME;
    }

    /**
     * Get subscribeToAll field id
     *
     * @return string
     */
    protected function getSubscribeToAllFieldId()
    {
        return Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Module\XC\MailChimp\Main::isMailChimpConfigured()
            && $this->getData(true) > 0;
    }
}
