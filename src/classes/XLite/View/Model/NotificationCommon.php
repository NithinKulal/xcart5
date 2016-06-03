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
class NotificationCommon extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = array(
        'emailNotificationCustomerHeader' => array(
            self::SCHEMA_CLASS => '\XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL => 'Customer header',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_TRUSTED  => true,
        ),
        'emailNotificationCustomerSignature' => array(
            self::SCHEMA_CLASS => '\XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL => 'Customer signature',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_TRUSTED  => true,
        ),
        'emailNotificationAdminHeader' => array(
            self::SCHEMA_CLASS => '\XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL => 'Administrator header',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_TRUSTED  => true,
        ),
        'emailNotificationAdminSignature' => array(
            self::SCHEMA_CLASS => '\XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL => 'Administrator signature',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_TRUSTED  => true,
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
        $list[] = 'notifications/headers_and_signatures.css';

        return $list;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Notification
     */
    protected function getDefaultModelObject()
    {
        return null;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Model\NotificationCommon';
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
                \XLite\View\Button\AButton::PARAM_LABEL => 'Update',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
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
        \XLite\Core\TopMessage::addInfo('The common notification fields has been updated');
    }

    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        return static::t($name);
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionUpdate()
    {
        \XLite\Core\Database::getEM()->flush();
        \XLite\Core\Translation::getInstance()->reset();

        return true;
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        foreach ($this->getFormFields(true) as $name) {
            if (isset($data[$name])) {
                $label = \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')->findOneByName($name);

                if ($label) {
                    $label->setLabel($data[$name]);
                }
            }
        }
    }

    /**
     * Prepare emailNotificationCustomerHeader field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsEmailNotificationCustomerHeader($data)
    {
        $data[\XLite\View\FormField\AFormField::PARAM_LABEL_HELP_WIDGET] = '\XLite\View\NotificationHelp';

        return $data;
    }

    /**
     * Prepare emailNotificationCustomerSignature field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsEmailNotificationCustomerSignature($data)
    {
        $data[\XLite\View\FormField\AFormField::PARAM_LABEL_HELP_WIDGET] = '\XLite\View\NotificationHelp';

        return $data;
    }

    /**
     * Prepare emailNotificationAdminHeader field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsEmailNotificationAdminHeader($data)
    {
        $data[\XLite\View\FormField\AFormField::PARAM_LABEL_HELP_WIDGET] = '\XLite\View\NotificationHelp';

        return $data;
    }

    /**
     * Prepare emailNotificationAdminSignature field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsEmailNotificationAdminSignature($data)
    {
        $data[\XLite\View\FormField\AFormField::PARAM_LABEL_HELP_WIDGET] = '\XLite\View\NotificationHelp';

        return $data;
    }
}
