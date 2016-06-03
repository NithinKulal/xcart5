<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Export\Step;

/**
 * Users
 */
class Users extends \XLite\Logic\Export\Step\AStep
{
    const ADDRESS_FIELD_SUFFIX = 'AddressField';

    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile');
    }

    /**
     * Get model datasets
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return array
     */
    protected function getModelDatasets(\XLite\Model\AEntity $model)
    {
        return $this->distributeDatasetModel(
            parent::getModelDatasets($model),
            'address',
            $model->getAddresses()
        );
    }

    /**
     * Get filename
     *
     * @return string
     */
    protected function getFilename()
    {
        return 'customers.csv';
    }

    // }}}

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = array(
            'login'           => array(),
            'added'           => array(),
            'firstLogin'      => array(),
            'lastLogin'       => array(),
            'status'          => array(),
            'referer'         => array(),
            'language'        => array(),
            'shippingAddress' => array(),
            'billingAddress'  => array(),
            'membership'      => array(),
        );

        if (\XLite\Core\Auth::getInstance()->isAdminProfilesManager() && $this->generator->getOptions()->itemsList) {
            $columns['access_level'] = array(
                static::COLUMN_GETTER    => 'getAccessLevelColumnValue',
            );
            $columns['roles'] = array(
                static::COLUMN_MULTIPLE  => true,
            );
        }

        foreach (\XLite\Core\Database::getRepo('XLite\Model\AddressField')->findAllEnabled() as $field) {
            $columns[$this->getColumnName($field)] = array(
                static::COLUMN_GETTER    => 'getAddressFieldValue',
                static::COLUMN_FORMATTER => 'formatAddressFieldValue',
                static::COLUMN_MULTIPLE  => true,
                'service_name'           => $field->getServiceName(),
            );
        }

        return $columns;
    }


    /**
     * Return column name
     *
     * @param \XLite\Model\AddressField $field Field
     *
     * @return string
     */
    protected function getColumnName(\XLite\Model\AddressField $field)
    {
        $name = $field->getServiceName();

        if ('state_id' == $name || 'custom_state' == $name) {
            $name = 'state';
        }

        $name = lcfirst(\XLite\Core\Converter::convertToCamelCase($name));

        return $name . static::ADDRESS_FIELD_SUFFIX;
    }

    // }}}

    // {{{ Getters and formatters

    /**
     * Get column value for 'login' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getLoginColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'login');
    }

    /**
     * Get column value for 'accessLevel' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getAccessLevelColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'access_level');
    }

    /**
     * Get column value for 'added' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getAddedColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'added');
    }

    /**
     * Format 'added' field value
     *
     * @param mixed  $value   Value
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return string
     */
    protected function formatAddedColumnValue($value, array $dataset, $name)
    {
        return $this->formatTimestamp($value);
    }

    /**
     * Get column value for 'firstLogin' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getFirstLoginColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'first_login');
    }

    /**
     * Format 'firstLogin' field value
     *
     * @param mixed  $value   Value
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return string
     */
    protected function formatFirstLoginColumnValue($value, array $dataset, $name)
    {
        return $this->formatTimestamp($value);
    }

    /**
     * Get column value for 'lastLogin' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getLastLoginColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'last_login');
    }

    /**
     * Format 'lastLogin' field value
     *
     * @param mixed  $value   Value
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return string
     */
    protected function formatLastLoginColumnValue($value, array $dataset, $name)
    {
        return $this->formatTimestamp($value);
    }

    /**
     * Get column value for 'status' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getStatusColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'status');
    }

    /**
     * Get column value for 'referer' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getRefererColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'referer');
    }

    /**
     * Get column value for 'language' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getLanguageColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'language');
    }

    /**
     * Get column value for 'shippingAddress' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getShippingAddressColumnValue(array $dataset, $name, $i)
    {
        return empty($dataset['address']) ? '' : $this->getColumnValueByName($dataset['address'], 'is_shipping');
    }

    /**
     * Get column value for 'billingAddress' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getBillingAddressColumnValue(array $dataset, $name, $i)
    {
        return empty($dataset['address']) ? '' : $this->getColumnValueByName($dataset['address'], 'is_billing');
    }

    /**
     * Get column value for 'membership' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getMembershipColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getMembership();
    }

    /**
     * Get column value for abstract address column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getAddressFieldValue(array $dataset, $name)
    {
        $column = $this->getColumn($name);

        if (empty($dataset['address'])) {
            $result = '';

        } elseif ('state_id' == $column['service_name'] || 'custom_state' == $column['service_name']) {
            $result = $dataset['address']->getStateName();

        } elseif ('country_code' == $column['service_name']) {
            $result = $dataset['address']->getCountryCode();

        } else {
            $result = $dataset['address']->getterProperty($column['service_name']);
        }

        return $result;
    }

    /**
     * Format address field value
     *
     * @param mixed  $value   Value
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return string
     */
    protected function formatAddressFieldValue($value, array $dataset, $name)
    {
        return $value;
    }

    /**
     * Get value for 'roles' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     *
     * @return string
     */
    protected function getRolesColumnValue(array $dataset, $name)
    {
        $result = array();

        foreach ($dataset['model']->getRoles() as $role) {
            $result[] = $role->getName();
        }

        return $result;
    }

    // }}}
}
