<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\CheckboxList;

/**
 * User type selector
 */
class UserType extends \XLite\View\FormField\Select\CheckboxList\ACheckboxList
{
    /**
     * Get user types
     *
     * @return array
     */
    protected function getUserTypes()
    {
        $types = array(
            'C' => static::t('Registered Customers'),
            'N' => static::t('Anonymous Customers'),
        );

        if (\XLite\Core\Auth::getInstance()->isPermissionAllowed('manage admins')) {
            $types['A'] = static::t('Administrator');
        }            
        return $types;
    }

    /**
     * Get roles
     *
     * @return array
     */
    protected function getRoles()
    {
        $list = array();

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Role')->findAll() as $role) {
            $list[$role->getId()] = $role->getPublicName();
        }

        return $list;
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();

        $list['C'] = array(
            'label' => static::t('Customer'),
            'options' => array(),
        );

        foreach ($this->getUserTypes() as $userType => $label) {
            if ('A' == $userType) {
                $list[$userType] = array(
                    'label' => $label,
                    'options' => $this->getRoles(),
                );
            } else {
                $list['C']['options'][$userType] = $label;
            }
        }

        ksort($list);

        return $list;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $list = parent::setCommonAttributes($attrs);
        $list['data-placeholder'] = static::t('All user types');

        return $list;
    }

}
