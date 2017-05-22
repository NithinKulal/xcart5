<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Import\Processor;

/**
 * Customers import processor
 */
class Customers extends \XLite\Logic\Import\Processor\AProcessor
{

    const ADDRESS_FIELD_SUFFIX = 'AddressField';

    /**
     * Runtime cache
     *
     * @var boolean
     */
    protected $isAdminProfileManager;

    /**
     * Flag: true if current row is for current authorized user
     *
     * @var boolean
     */
    protected $isCurrentUser;

    /**
     * Login value for importing row
     *
     * @var string
     */
    protected $login;

    /**
     * Flag: true - row will be ignored
     *
     * @var boolean
     */
    protected $isIgnoreRow = false;

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Customers imported');
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile');
    }

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = array(
            'login'           => array(
                static::COLUMN_IS_KEY => true,
                static::COLUMN_LENGTH => 128,
            ),
            'added'           => array(),
            'firstLogin'      => array(),
            'lastLogin'       => array(),
            'status'          => array(),
            'referer'         => array(
                static::COLUMN_LENGTH => 255,
            ),
            'language'        => array(
                static::COLUMN_LENGTH => 2,
            ),
            'membership'      => array(),
            'addressField'    => array(
                static::COLUMN_IS_MULTICOLUMN  => true,
                static::COLUMN_IS_MULTIROW     => true,
                static::COLUMN_HEADER_DETECTOR => true,
            ),
            'password'        => array(
                static::COLUMN_LENGTH => 128,
            ),
            'forceChangePassword' => array(),
            'access_level' => array(),
            'roles' => array(
                static::COLUMN_IS_MULTIPLE => true,
            ),
        );

        return $columns;
    }

    /**
     * Return true if current authorized user can manage administrator profiles
     *
     * @return boolean
     */
    protected function isAdminProfilesManager()
    {
        if (!isset($this->isAdminProfilesManager)) {
            $this->isAdminProfilesManager = \XLite\Core\Auth::getInstance()->isAdminProfilesManager();
        }

        return $this->isAdminProfilesManager;
    }

    // }}}

    // {{{ Header detectors

    /**
     * Detect address field header(s)
     *
     * @param array $column Column info
     * @param array $row    Header row
     *
     * @return array
     */
    protected function detectAddressFieldHeader(array $column, array $row)
    {
        return $this->detectHeaderByPattern(
            '(?:\w+' . static::ADDRESS_FIELD_SUFFIX . '|shippingAddress|billingAddress)',
            $row
        );
    }

    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + array(
                'USER-LOGIN-FMT'      => 'Wrong login format',
                'USER-ADDED-FMT'      => 'Wrong added format',
                'USER-FLOGIN-FMT'     => 'Wrong first login date format',
                'USER-LLOGIN-FMT'     => 'Wrong last login date format',
                'USER-STATUS-FMT'     => 'Wrong status format',
                'USER-REFERER-FMT'    => 'Wrong referer format',
                'USER-LANGUAGE-FMT'   => 'Wrong language format',
                'USER-SHPADDR-FMT'    => 'Wrong shipping address format',
                'USER-BILADDR-FMT'    => 'Wrong billing address format',
                'USER-CCODE-FMT'      => 'Wrong country code format',
                'USER-SID-FMT'        => 'Wrong state id format',
                'USER-ACCESSLEV-PERM' => 'There are no permissions to import user access level',
                'USER-ACCESSLEV-FMT'  => 'Wrong access level format',
                'USER-ROLES-PERM'     => 'There are no permissions to import user roles',
                'USER-ROLE-FMT'       => 'Wrong role format',
                'USER-ROLES-SELF'     => 'Roles cannot be changed for your profile via import',
                'USER-ACCESSLEV-CHANGE' => 'Access level cannot be changed (from {{prevValue}} to {{value}}) for existing profile ({{login}})',
                'USER-ADMIN-IGN'      => 'You cannot update administrator profile ({{value}}). Row will be skipped',
                'USER-ADMIN-IMP-IGN'  => 'You cannot update administrator profile ({{value}}). Row is skipped',
            );
    }

    /**
     * Verify data chunk
     *
     * @param array $data Data chunk
     *
     * @return boolean
     */
    protected function verifyData(array $data)
    {
        $this->isIgnoreRow = false;

        return parent::verifyData($data);
    }

    /**
     * Verify cell
     *
     * @param array $column Column info
     * @param mixed $value  Value
     *
     * @return void
     */
    protected function verifyCell(array $column, $value)
    {
        if (!$this->isIgnoreRow) {
            parent::verifyCell($column, $value);
        }
    }

    /**
     * Verify 'login' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyLogin($value, array $column)
    {
        if (!$this->verifyValueAsEmail($value)) {
            $this->addError('USER-LOGIN-FMT', array('column' => $column, 'value' => $value));
        }

        $this->login = $value;
        $this->isCurrentUser = \XLite\Core\Auth::getInstance()->getProfile()
            && $value == \XLite\Core\Auth::getInstance()->getProfile()->getLogin();

        if (!$this->isAdminProfilesManager()) {
            $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($this->login);

           if ($profile && $profile->isAdmin()) {
               $this->addWarning('USER-ADMIN-IGN', array('column' => $column, 'value' => $value));
               $this->isIgnoreRow = true;
           }
        }
    }

    /**
     * Verify 'password' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyPassword($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {
            // Schedule to delete files after import finished
            $this->importer->getOptions()->clearImportDir = true;
        }
    }

    /**
     * Verify 'added date' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyAdded($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsDate($value)) {
            $this->addWarning('USER-ADDED-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'first login' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyFirstLogin($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsDate($value)) {
            $this->addWarning('USER-FLOGIN-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'last login' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyLastLogin($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsDate($value)) {
            $this->addWarning('USER-LLOGIN-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'status' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyStatus($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)
            && !$this->verifyValueAsSet(
                $value,
                array(\XLite\Model\Profile::STATUS_ENABLED, \XLite\Model\Profile::STATUS_DISABLED)
            )
        ) {
            $this->addError('USER-STATUS-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'language' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyLanguage($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsLanguageCode($value)) {
            $this->addWarning('USER-LANGUAGE-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'shipping address' value
     *
     * @param mixed   $value  Value
     * @param array   $column Column info
     * @param integer $index Row offset
     *
     * @return void
     */
    protected function verifyShippingAddress($value, array $column, $index)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('USER-SHPADDR-FMT', array('column' => $column, 'value' => $value), $index);
        }
    }

    /**
     * Verify 'billing address' value
     *
     * @param mixed   $value  Value
     * @param array   $column Column info
     * @param integer $index  Row offset
     *
     * @return void
     */
    protected function verifyBillingAddress($value, array $column, $index)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('USER-BILADDR-FMT', array('column' => $column, 'value' => $value), $index);
        }
    }

    /**
     * Verify 'address field' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyAddressField($value, array $column)
    {
        if (is_array($value)) {
            foreach ($value as $name => $rows) {
                $method = null;
                if ($this->isColumnHeaderEqual('shippingAddress', $name)) {
                    $method = 'verifyShippingAddress';

                } elseif ($this->isColumnHeaderEqual('billingAddress', $name)) {
                    $method = 'verifyBillingAddress';

                } else {
                    $serviceName = $this->normalizeColumnHeader('(\w+)' . static::ADDRESS_FIELD_SUFFIX, $name);
                    if ($serviceName) {
                        $method = 'verifyAddressField' . \XLite\Core\Converter::convertToCamelCase($serviceName);
                        if (!method_exists($this, $method)) {
                            $method = null;
                        }
                    }
                }

                if ($method) {
                    foreach ($rows as $i => $v) {
                        $this->$method($v, $column, $i);
                    }
                }
            }
        }
    }

    /**
     * Verify 'address field country code' value
     *
     * @param mixed   $value  Value
     * @param array   $column Column info
     * @param integer $index  Row offset
     *
     * @return void
     */
    protected function verifyAddressFieldCountryCode($value, array $column, $index)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsCountryCode($value)) {
            $this->addWarning('USER-CCODE-FMT', array('column' => $column, 'value' => $value), $index);
        }
    }

    /**
     * Verify 'address field state Id' value
     *
     * @param mixed   $value  Value
     * @param array   $column Column info
     * @param integer $index  Row offset
     *
     * @return void
     */
    protected function verifyAddressFieldStateId($value, array $column, $index)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsStateId($value)) {
            $this->addWarning('USER-SID-FMT', array('column' => $column, 'value' => $value), $index);
        }
    }

    /**
     * Verify 'membership' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMembership($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsMembership($value)) {
            $this->addWarning('GLOBAL-MEMBERSHIP-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'forceChangePassword' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyForceChangePassword($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('USER-FORDER-CHANGE-PASSWORD-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify access_level value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyAccessLevel($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {

            if (!$this->isAdminProfilesManager()) {
                // Administrator hasn't permissions to change access level
                $this->addError('USER-ACCESSLEV-PERM', array('column' => $column));

            } elseif (!in_array($value, array(\XLite\Core\Auth::getInstance()->getAdminAccessLevel(), \XLite\Core\Auth::getInstance()->getCustomerAccessLevel()))) {
                // Wrong value of access level
                $this->addError('USER-ACCESSLEV-FMT', array('column' => $column, 'value' => $value));

            } else {

                $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($this->login);

                if ($profile && (int)$value != (int)$profile->getAccessLevel()) {
                    // It's impossible to change access level of an existing profile
                    $this->addWarning('USER-ACCESSLEV-CHANGE', array('column' => $column, 'value' => $value, 'prevValue' => $profile->getAccessLevel(), 'login' => $profile->getLogin()));
                }
            }
        }
    }

    /**
     * Verify roles value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyRoles($value, array $column)
    {
        if (!empty($value)) {

            if (!$this->isAdminProfilesManager()) {
                // Administrator hasn't permissions to change access level
                $this->addError('USER-ROLES-PERM', array('column' => $column));

            } elseif ($this->isCurrentUser) {
                // Admin cannot change its own roles via import
                $this->addWarning('USER-ROLES-SELF', array('column' => $column));

            } elseif (is_array($value)) {
                foreach ($value as $role) {
                    if (!$this->verifyValueAsEmpty($role) && !$this->verifyValueAsRole($role)) {
                        // Wrong role name
                        $this->addError('USER-ROLE-FMT', array('column' => $column, 'value' => $role));
                    }
                }
            }
        }
    }

    /**
     * Return true if value is an existing role
     *
     * @param string $value Role name
     *
     * @return boolean
     */
    protected function verifyValueAsRole($value)
    {
        $result = false;

        if ($value) {
            $result = \XLite\Core\Database::getRepo('XLite\Model\Role')->findOneByName($value);
        }

        return (bool) $result;
    }

    // }}}

    // {{{ Import

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        \Xlite\Core\Database::getRepo('XLite\Model\Product')->setBlockQuickDataFlag(true);

        return parent::importData($data);
    }

    /**
     * Import password
     *
     * @param \XLite\Model\Profile $model Profile
     * @param string               $value Value
     * @param integer              $index Index
     *
     * @return void
     */
    protected function importPasswordColumn(\XLite\Model\Profile $model, $value, $index)
    {
        if (!empty($value)) {
            $model->setPassword(\XLite\Core\Auth::encryptPassword($value));
            // Schedule to delete files after import finished
            $this->importer->getOptions()->clearImportDir = true;
        }
    }

    /**
     * Import 'address field' value
     *
     * @param \XLite\Model\Profile $model  Profile
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importAddressFieldColumn(\XLite\Model\Profile $model, array $value, array $column)
    {
        if ($value && !$this->verifyValueAsNull($value)) {
            $data = array();
            foreach ($value as $name => $rows) {
                $method = null;
                if ($this->isColumnHeaderEqual('shippingAddress', $name)) {
                    $data['is_shipping'] = $rows;

                } elseif ($this->isColumnHeaderEqual('billingAddress', $name)) {
                    $data['is_billing'] = $rows;

                } else {
                    $serviceName = $this->normalizeColumnHeader('(\w+)' . static::ADDRESS_FIELD_SUFFIX, $name);
                    if ($serviceName) {
                        if ('state' == $serviceName) {
                            $data['state'] = $rows;

                        } else {
                            foreach (\XLite\Core\Database::getRepo('XLite\Model\AddressField')->findAllEnabled() as $field) {
                                $fname = lcfirst(\XLite\Core\Converter::convertToCamelCase($field->getServiceName()));
                                if ($fname == $serviceName) {
                                    $data[$field->getServiceName()] = $rows;
                                }
                            }
                        }
                    }
                }
            }

            $addresses = $this->assembleSubmodelsData($data, $column);

            $i = 0;
            foreach ($addresses as $address) {
                $this->importAddress($model, $address, $i);
                $i++;
            }

            // Remove
            while (count($model->getAddresses()) > count($addresses)) {
                $address = $model->getAddresses()->last();
                \XLite\Core\Database::getRepo('XLite\Model\Address')->delete($address, false);
                $model->getAddresses()->removeElement($address);
            }
        } elseif ($value && $this->verifyValueAsNull($value)) {
            $model->getAddresses()->clear();
        }
    }

    /**
     * Import address
     *
     * @param \XLite\Model\Profile $model   Profile
     * @param array                $address Address
     * @param integer              $index   Index
     *
     * @return void
     */
    protected function importAddress(\XLite\Model\Profile $model, array $address, $index)
    {
        $addr = $model->getAddresses()->get($index);
        if (!$addr) {
            $addr = $this->createAddress();
            $model->addAddresses($addr);
            $addr->setProfile($model);
        }

        if (isset($address['is_shipping'])) {
            $address['is_shipping'] = $this->normalizeValueAsBoolean($address['is_shipping']);
        }

        if (isset($address['is_billing'])) {
            $address['is_billing'] = $this->normalizeValueAsBoolean($address['is_billing']);
        }

        if (isset($address['state'])) {
            $address['state'] = $this->normalizeValueAsState($address['state']);
        }

        $this->updateAddress($addr, $address);
    }

    /**
     * Import access_level: allowed to set access level only on create new profile
     *
     * @param \XLite\Model\Profile $model  Profile
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importAccessLevelColumn(\XLite\Model\Profile $model, $value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)
            && $this->isAdminProfilesManager()
            && $model->getLogin() != \XLite\Core\Auth::getInstance()->getProfile()->getLogin()
            && $this->isNewModel
        ) {
            $model->setAccessLevel($value);
        }
    }

    /**
     * Import roles
     *
     * @param \XLite\Model\Profile $model  Profile
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importRolesColumn(\XLite\Model\Profile $model, array $value, array $column)
    {
        if (
            !$this->verifyValueAsEmpty($value)
            && $this->isAdminProfilesManager()
            && $model->getLogin() != \XLite\Core\Auth::getInstance()->getProfile()->getLogin()
        ) {

            if ($model->getRoles()) {
                foreach ($model->getRoles() as $role) {
                    $role->getProfiles()->removeElement($model);
                }
                $model->getRoles()->clear();
            }

            if (!$this->verifyValueAsNull($value)) {
                foreach ($value as $role) {
                    $role = $this->normalizeRoleValue($role);
                    if ($role) {
                        $model->addRoles($role);
                        $role->addProfiles($model);
                    }
                }
            }
        }
    }

    /**
     * Insert address
     *
     * @return \XLite\Model\Address
     */
    protected function createAddress()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Address')->insert(null, false);
    }

    /**
     * Update address
     *
     * @param \XLite\Model\Address $address Address to update
     * @param array                $data    New values for address
     *
     * @return void
     */
    protected function updateAddress(\XLite\Model\Address $address, array $data)
    {
        \XLite\Core\Database::getRepo('XLite\Model\Address')->update($address, $data, false);
    }

    // }}}

    // {{{ Normalizators

    /**
     * Normalize 'login' value
     *
     * @param mixed @value Value
     *
     * @return string
     */
    protected function normalizeLoginValue($value)
    {
        return $this->normalizeValueAsEmail($value);
    }

    /**
     * Normalize 'added' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizeAddedValue($value)
    {
        return $this->normalizeValueAsDate($value);
    }

    /**
     * Normalize 'first login' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizeFirstLoginValue($value)
    {
        return $this->normalizeValueAsDate($value);
    }

    /**
     * Normalize 'last login' value
     *
     * @param mixed @value Value
     *
     * @return intgern
     */
    protected function normalizeLastLoginValue($value)
    {
        return $this->normalizeValueAsDate($value);
    }

    /**
     * Normalize 'status' value
     *
     * @param mixed @value Value
     *
     * @return string
     */
    protected function normalizeStatusValue($value)
    {
        return strtoupper($value);
    }

    /**
     * Normalize 'language' value
     *
     * @param mixed @value Value
     *
     * @return string
     */
    protected function normalizeLanguageValue($value)
    {
        return strtolower($value);
    }

    /**
     * Normalize 'membership' value
     *
     * @param mixed @value Value
     *
     * @return \XLite\Model\Membership
     */
    protected function normalizeMembershipValue($value)
    {
        return $this->normalizeValueAsMembership($value);
    }

    /**
     * Normalize 'role' value
     *
     * @param string $value Role code
     *
     * @return \XLite\Model\Role
     */
    protected function normalizeRoleValue($value)
    {
        $result = null;

        if ($value) {
            $result = \XLite\Core\Database::getRepo('XLite\Model\Role')->findOneByName($value);
        }

        return $result;
    }

    // }}}

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        $entity = parent::detectModel($data);

        // Detected model is an admin profile and current user hasn't permissions to manage admins - ignore row
        $this->isIgnoreRow = $entity
            && !$this->isAdminProfilesManager()
            && $entity->isAdmin();

        if ($this->isIgnoreRow) {
            $this->addWarning('USER-ADMIN-IMP-IGN', array('value' => $entity->getLogin()));
            $this->setMetaData('updateCount', ((int) $this->getMetaData('updateCount')) - 1);
        }

        $this->isNewModel = !(bool)$entity;

        return $entity;
    }

    /**
     * Create model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function createModel(array $data)
    {
        $entity = null;

        if (!$this->isIgnoreRow) {
            $entity = parent::createModel($data);

            $entity->updateSearchFakeField();
        }

        return $entity;
    }

    /**
     * Update model
     *
     * @param \XLite\Model\AEntity $model Model
     * @param array                $data  Data
     *
     * @return boolean
     */
    protected function updateModel(\XLite\Model\AEntity $model, array $data)
    {
        $result = false;

        if (!$this->isIgnoreRow) {
            $result = parent::updateModel($model, $data);

            if ($result) {
                $model->updateSearchFakeField();
            }
        }

        return $result;
    }
}
