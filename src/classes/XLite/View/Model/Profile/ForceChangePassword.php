<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model\Profile;

/**
 * \XLite\View\Model\Profile\Main
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class ForceChangePassword extends \XLite\View\Model\Profile\Main
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        if (\XLite::isAdminZone()) {
            $result[] = 'force_change_password';
        }

        return $result;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Change password';
    }

    /**
     * Return text for header
     *
     * @return string
     */
    protected function getHeaderText()
    {
        return static::t('Admin has requested a change of password for your account. Please change the password before you proceed.');
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' change-password-form-container';
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionMain()
    {
        // Password is required
        unset($this->mainSchema['login']);
        unset($this->mainSchema['membership_id']);
        unset($this->mainSchema['pending_membership_id']);
        foreach (array('password', 'password_conf') as $field) {
            if (isset($this->mainSchema[$field])) {
                $this->mainSchema[$field][self::SCHEMA_REQUIRED] = true;
            }
        }

        return $this->getFieldsBySchema($this->mainSchema);
    }

    /**
     * Return text for the "Submit" button
     *
     * @return string
     */
    protected function getSubmitButtonLabel()
    {
        return 'Update';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();
        if ($this->isLogged()) {
            unset($result['delete_profile']);
        }

        return $result;
    }

    /**
     * Prepare posted data for mapping to the object
     *
     * @return array
     */
    protected function prepareDataForMapping()
    {
        $data = parent::prepareDataForMapping();
        if (!empty($data['password'])) {
            $data['forceChangePassword'] = false;
        }

        return $data;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Profile\ForceChangePassword';
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
        $result = !\XLite\Core\Auth::comparePassword(
            \XLite\Core\Auth::getInstance()->getProfile()->getPassword(), $data['password']
        );

        if (!$result) {
            $formFields = $this->getFormFields();
            $this->addErrorMessage(
                'password',
                'The new password must not coincide with the current password for your account.',
                $formFields[self::SECTION_MAIN]
            );
        }

        parent::setModelProperties($data);
    }

    /**
     * Update profile
     *
     * @return boolean
     */
    protected function performActionUpdate()
    {
        $result = parent::performActionUpdate();
        if ($this->isValid()) {
            \XLite\Core\Session::getInstance()->forceChangePassword = false;
        }

        return $result;
    }
}
