<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\FormModel\Settings\Notification;

class Notification extends \XLite\View\FormModel\Settings\Notification\Notification implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        $result = parent::defineFields();

        if (isset($result['scheme']['body'])) {
            $result['scheme']['body'] = array_replace(
                $result['scheme']['body'],
                [
                    'type' => 'XLite\Module\XC\ThemeTweaker\View\FormModel\Type\NotificationBodyType',
                    'url'  => $this->getEditBodyURL(),
                ]
            );
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getEditBodyURL()
    {
        if ($this->isBodyEditable()) {
            $templatesDirectory = $this->getDataObject()->default->templatesDirectory;
            $interface = $this->getDataObject()->default->page === 'admin'
                ? \XLite::ADMIN_INTERFACE
                : \XLite::CUSTOMER_INTERFACE;

            return $this->buildURL(
                'notification_editor',
                '',
                [
                    'templatesDirectory' => $templatesDirectory,
                    'interface'          => $interface,
                ]
            );
        }

        return '';
    }

    /**
     * @return boolean
     */
    protected function isBodyEditable()
    {
        return $this->isOrderNotification();
    }

    /**
     * @return boolean
     */
    protected function isOrderNotification()
    {
        $templateDirectory = $this->getDataObject()->default->templatesDirectory;

        return \XLite\Module\XC\ThemeTweaker\Main::isOrderNotification($templateDirectory);
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $list = parent::getFormButtons();

        if ($this->isOrderNotification()) {
            if (\XLite\Module\XC\ThemeTweaker\Main::getDumpOrder()) {

                $url = $this->buildURL(
                    'notification',
                    'send_test_email',
                    [
                        'templatesDirectory' => $this->getDataObject()->default->templatesDirectory,
                        'page'               => $this->getDataObject()->default->page,
                    ]
                );
                $list['send_test_email'] = new \XLite\View\Button\Link(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL => 'Send test email',
                        \XLite\View\Button\AButton::PARAM_STYLE => 'action always-enabled',
                        \XLite\View\Button\Link::PARAM_LOCATION => $url,
                    ]
                );

                $url = $this->buildURL(
                    'notification',
                    '',
                    [
                        'templatesDirectory' => $this->getDataObject()->default->templatesDirectory,
                        'page'               => $this->getDataObject()->default->page,
                        'preview'            => true,
                    ]
                );
                $list['preview_template'] = new \XLite\View\Button\Link(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL => 'Preview template',
                        \XLite\View\Button\AButton::PARAM_STYLE => 'action always-enabled',
                        \XLite\View\Button\Link::PARAM_BLANK    => true,
                        \XLite\View\Button\Link::PARAM_LOCATION => $url,
                    ]
                );
            } else {
                $list['send_test_email'] = new \XLite\View\Button\Tooltip(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL          => 'Send test email',
                        \XLite\View\Button\AButton::PARAM_STYLE          => 'action',
                        \XLite\View\Button\AButton::PARAM_DISABLED       => true,
                        \XLite\View\Button\Tooltip::PARAM_BUTTON_TOOLTIP => static::t('No orders available. Please create at least one order.'),
                    ]
                );

                $list['preview_template'] = new \XLite\View\Button\Tooltip(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL          => 'Preview template',
                        \XLite\View\Button\AButton::PARAM_STYLE          => 'action',
                        \XLite\View\Button\AButton::PARAM_DISABLED       => true,
                        \XLite\View\Button\Tooltip::PARAM_BUTTON_TOOLTIP => static::t('No orders available. Please create at least one order.'),
                    ]
                );
            }
        }

        return $list;
    }

    /**
     * Return form theme files. Used in template.
     *
     * @return array
     */
    protected function getFormThemeFiles()
    {
        $list = parent::getFormThemeFiles();
        $list[] = 'modules/XC/ThemeTweaker/form_model/settings/notification/notification.twig';

        return $list;
    }
}
