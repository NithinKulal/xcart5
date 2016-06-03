<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Notification view model
 */
class Notification extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = array(
        'enabledForAdmin' => array(
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL    => 'Administrator',
            self::SCHEMA_REQUIRED => false,
        ),
        'enabledForCustomer' => array(
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL    => 'Customer',
            self::SCHEMA_REQUIRED => false,
        ),
        'customerSubject' => array(
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Customer subject',
            self::SCHEMA_REQUIRED => false,
        ),
        'customerText' => array(
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL    => 'Customer text',
            self::SCHEMA_REQUIRED => false,
        ),
        'adminSubject' => array(
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Administrator subject',
            self::SCHEMA_REQUIRED => false,
        ),
        'adminText' => array(
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL    => 'Administrator text',
            self::SCHEMA_REQUIRED => false,
        ),
        'name' => array(
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Name',
            self::SCHEMA_REQUIRED => true,
        ),
        'description' => array(
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL    => 'Description',
            self::SCHEMA_REQUIRED => true,
            \XLite\View\FormField\Textarea\Simple::PARAM_ROWS => 2,
        ),
    );

    /**
     * Returns CSS Files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'notification/style.css';

        return $list;
    }

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->templatesDirectory;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Notification
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Model\Notification')->find($this->getModelId())
            : null;

        return $model;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Model\Notification';
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
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Update',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            )
        );

        $result['notifications'] = new \XLite\View\Button\SimpleLink(
            array(
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('notifications'),
                \XLite\View\Button\AButton::PARAM_LABEL => 'Back to notifications list',
                \XLite\View\Button\AButton::PARAM_STYLE => 'action',
            )
        );

        return $result;
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        \XLite\Core\TopMessage::addInfo('The notification has been updated');
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        $notification = $this->getModelObject();

        if ($notification->isPersistent()) {
            if (!$notification->getAvailableForAdmin()) {
                if ($notification->getEnabledForAdmin()) {
                    $this->schemaDefault['enabledForAdmin'][\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_DISABLED] = true;
                } else {
                    unset($this->schemaDefault['enabledForAdmin']);
                }
            }

            if (!$notification->getAvailableForAdmin() && !$notification->getEnabledForAdmin()) {
                unset($this->schemaDefault['adminSubject']);
                unset($this->schemaDefault['adminText']);
            }

            if (!$notification->getAvailableForCustomer()) {
                if ($notification->getEnabledForCustomer()) {
                    $this->schemaDefault['enabledForCustomer'][\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_DISABLED] = true;
                } else {
                    unset($this->schemaDefault['enabledForCustomer']);
                }
            }

            if (!$notification->getAvailableForCustomer() && !$notification->getEnabledForCustomer()) {
                unset($this->schemaDefault['customerSubject']);
                unset($this->schemaDefault['customerText']);
            }
        }

        return $this->translateSchema('default');
    }

    /**
     * Prepare customerSubject field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsCustomerSubject($data)
    {
        $data[\XLite\View\FormField\AFormField::PARAM_LABEL_HELP_WIDGET] = '\XLite\View\NotificationHelp';

        return $data;
    }

    /**
     * Prepare customerText field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsCustomerText($data)
    {
        $data[\XLite\View\FormField\AFormField::PARAM_LABEL_HELP_WIDGET] = '\XLite\View\NotificationHelp';

        return $data;
    }

    /**
     * Prepare adminSubject field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsAdminSubject($data)
    {
        $data[\XLite\View\FormField\AFormField::PARAM_LABEL_HELP_WIDGET] = '\XLite\View\NotificationHelp';

        return $data;
    }

    /**
     * Prepare adminText field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsAdminText($data)
    {
        $data[\XLite\View\FormField\AFormField::PARAM_LABEL_HELP_WIDGET] = '\XLite\View\NotificationHelp';

        return $data;
    }
}
