<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model\Profile;

/**
 * Administrator profile model widget. This widget is used in the admin interface
 */
class AdminMain extends \XLite\View\Model\AModel
{
    /**
     * Form sections
     */
    const SECTION_SUMMARY = 'summary';
    const SECTION_MAIN    = 'main';
    const SECTION_ACCESS  = 'access';

    /**
     * Schema of the "Account summary" section
     *
     * @var array
     */
    protected $summarySchema = [
        'referer'      => [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Label',
            self::SCHEMA_LABEL    => 'Referer',
            self::SCHEMA_REQUIRED => false,
        ],
        'added'        => [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Label',
            self::SCHEMA_LABEL    => 'Added',
            self::SCHEMA_REQUIRED => false,
        ],
        'last_login'   => [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Label',
            self::SCHEMA_LABEL    => 'Last login',
            self::SCHEMA_REQUIRED => false,
        ],
        'language'     => [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Label',
            self::SCHEMA_LABEL    => 'Language',
            self::SCHEMA_REQUIRED => false,
        ],
        'orders_count' => [
            self::SCHEMA_CLASS                                 => '\XLite\View\FormField\Label',
            self::SCHEMA_LABEL                                 => 'Orders count',
            self::SCHEMA_REQUIRED                              => false,
            \XLite\View\FormField\Label\ALabel::PARAM_UNESCAPE => true,
        ],
    ];

    /**
     * Schema of the "E-mail & Password" section
     *
     * @var array
     */
    protected $mainSchema = [
        'login'         => [
            self::SCHEMA_CLASS            => '\XLite\View\FormField\Input\Text\Email',
            self::SCHEMA_LABEL            => 'E-mail',
            self::SCHEMA_REQUIRED         => true,
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
        'password'      => [
            self::SCHEMA_CLASS            => '\XLite\View\FormField\Input\Password',
            self::SCHEMA_LABEL            => 'Password',
            self::SCHEMA_REQUIRED         => false,
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
        'password_conf' => [
            self::SCHEMA_CLASS            => '\XLite\View\FormField\Input\Password',
            self::SCHEMA_LABEL            => 'Confirm password',
            self::SCHEMA_REQUIRED         => false,
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
    ];

    /**
     * Schema of the "User access" section
     *
     * @var array
     */
    protected $accessSchema = [
        'access_level'          => [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Select\AccessLevel',
            self::SCHEMA_LABEL    => 'Access level',
            self::SCHEMA_REQUIRED => true,
        ],
        'status'                => [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Select\AccountStatus',
            self::SCHEMA_LABEL    => 'Account status',
            self::SCHEMA_REQUIRED => true,
        ],
        'statusComment'         => [
            self::SCHEMA_CLASS            => '\XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL            => 'Status comment (reason)',
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
            self::SCHEMA_REQUIRED         => false,
        ],
        'membership_id'         => [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Select\Membership',
            self::SCHEMA_LABEL    => 'Membership',
            self::SCHEMA_REQUIRED => false,
        ],
        'pending_membership_id' => [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Label',
            self::SCHEMA_LABEL    => 'Pending membership',
            self::SCHEMA_REQUIRED => false,
        ],
        'roles'                 => [
            self::SCHEMA_CLASS      => '\XLite\View\FormField\Select\Tags\Roles',
            self::SCHEMA_LABEL      => 'Roles',
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_SHOW => [
                    'access_level' => [100],
                ],
            ],
        ],
        'forceChangePassword'   => [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Require to change password on next log in',
            self::SCHEMA_REQUIRED => false,
        ],
    ];

    /**
     * Return value for the "register" mode param
     *
     * @return string
     */
    public static function getRegisterMode()
    {
        return \XLite\Controller\Admin\Profile::getRegisterMode();
    }

    /**
     * Save current form reference and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     */
    public function __construct(array $params = [], array $sections = [])
    {
        $this->sections = $this->getProfileMainSections() + $this->sections;

        parent::__construct($params, $sections);
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'model/profile/email.js';

        return $list;
    }

    /**
     * The "mode" parameter used to determine if we create new or modify existing profile
     *
     * @return boolean
     */
    public function isRegisterMode()
    {
        return \XLite\Controller\Admin\Profile::getInstance()->isRegisterMode();
    }

    /**
     * Return current profile ID
     *
     * @param boolean $checkMode Check mode or not OPTIONAL
     *
     * @return integer
     */
    public function getProfileId($checkMode = true)
    {
        return ($this->isRegisterMode() && $checkMode) ?:
            ($this->getRequestProfileId()) ?: \XLite\Core\Session::getInstance()->get('profile_id');
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
     * Check for the form errors
     *
     * @return boolean
     */
    public function isValid()
    {
        return ('validateInput' === $this->currentAction) ?: parent::isValid();
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/profile/main.css';

        return $list;
    }

    /**
     * getDefaultFieldValue
     *
     * @param string $name Field name
     *
     * @return mixed
     */
    public function getDefaultFieldValue($name)
    {
        $value = parent::getDefaultFieldValue($name);

        switch ($name) {

            case 'added':
            case 'last_login':
                if (0 < $value) {
                    $value = date('r', $value);

                } else {
                    $value = 'never';
                }

                break;

            case 'referer':
                $value = $value ?: 'unknown';
                break;

            case 'orders_count':
                if ($value) {
                    $url = $this->buildURL(
                        'order_list',
                        'searchByCustomer',
                        [
                            'profileId'     => $this->getModelObject()->getProfileId(),
                            \XLite::FORM_ID => \XLite::getFormId(),
                        ]
                    );

                    $value = '<a href="' . $url . '">' . $value . '</a>';

                } else {
                    $value = 'n/a';
                }

                break;

            case 'language':
                $lng = $value ? \XLite\Core\Database::getRepo('XLite\Model\Language')->findOneByCode($value) : null;
                $value = isset($lng) ? $lng->getName() : $value;
                break;

            case 'pending_membership_id':
                $value = 0 < $value ? $this->getModelObject()->getPendingMembership()->getName() : static::t('none');
                break;

            case 'roles':
                if ($this->getModelObject()
                    && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
                    && \XLite\Core\Auth::getInstance()->getProfile()->getProfileId() == $this->getModelObject()->getProfileId()
                ) {
                    if ($value) {
                        $roles = [];
                        /** @var \XLite\Model\Role $role */
                        foreach ($value as $role) {
                            $roles[] = $role->getPublicName();
                        }

                        $value = implode(', ', $roles);
                    }
                }

                break;

            case 'forceChangePassword':
                $value = $this->isRegisterMode() ? true : $this->getModelObject()->getForceChangePassword();
                break;

            default:
        }

        return $value;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Profile
     */
    protected function getDefaultModelObject()
    {
        if ($this->isRegisterMode()) {
            $obj = new \XLite\Model\Profile();

        } else {
            $obj = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($this->getProfileId());
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
        return '\XLite\View\Form\Profile\AdminMain';
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Profile details';
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionMain()
    {
        if ($this->getModelObject()) {
            if (!$this->getModelObject()->isPersistent()) {
                // Create new profile - password is required
                foreach (['password', 'password_conf'] as $field) {
                    if (isset($this->mainSchema[$field])) {
                        $this->mainSchema[$field][self::SCHEMA_REQUIRED] = true;
                    }
                }
            } elseif ($this->getModelObject()->getAnonymous()) {
                // Anonymous user
                foreach (['password', 'password_conf'] as $field) {
                    if (isset($this->mainSchema[$field])) {
                        unset($this->mainSchema[$field]);
                    }
                }

                if (isset($this->mainSchema['login'])) {
                    $this->mainSchema['login'][static::SCHEMA_CLASS] = 'XLite\View\FormField\Label';
                    $this->mainSchema['login'][static::SCHEMA_REQUIRED] = false;
                }
            } elseif ($this->getModelObject()->getOrdersCount()) {
                if (isset($this->mainSchema['login'])) {
                    $this->mainSchema['login'][static::SCHEMA_COMMENT] = static::t('E-mail will also be updated in all the related orders.');
                }
            }
        }

        return $this->getFieldsBySchema($this->mainSchema);
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionAccess()
    {
        // Logged user cannot see own status
        if ($this->isLoggedProfile() || $this->getModelObject()->getAnonymous()) {
            unset($this->accessSchema['status']);
            unset($this->accessSchema['statusComment']);
        }

        // New profile cannot see pending membership
        if ($this->isRegisterMode()) {
            unset($this->accessSchema['pending_membership_id']);

        } else {
            $this->accessSchema['access_level'][static::SCHEMA_CLASS] = '\XLite\View\FormField\Label';
            $this->accessSchema['access_level'][static::SCHEMA_REQUIRED] = false;
            $this->accessSchema['access_level'][\XLite\View\FormField\Label::PARAM_UNESCAPE] = true;

            unset($this->accessSchema['roles'][static::SCHEMA_DEPENDENCY]);
        }

        if ($this->getModelObject() && $this->getModelObject()->getAnonymous()) {
            unset($this->accessSchema['membership_id']);
            if (isset($this->accessSchema['pending_membership_id'])) {
                unset($this->accessSchema['pending_membership_id']);
            }
        }

        if (!\XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
            || (
                $this->getModelObject()
                && $this->getModelObject()->getProfileId()
                && !$this->getModelObject()->isAdmin()
            )
            || (2 > \XLite\Core\Database::getRepo('XLite\Model\Role')->count())
        ) {
            unset($this->accessSchema['roles']);
        }

        if ($this->getModelObject()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
            && \XLite\Core\Auth::getInstance()->getProfile()->getProfileId() == $this->getModelObject()->getProfileId()
        ) {
            unset($this->accessSchema['forceChangePassword']);
            $this->accessSchema['roles'][static::SCHEMA_CLASS] = '\XLite\View\FormField\Label';
            $this->accessSchema['roles'][static::SCHEMA_REQUIRED] = false;
        }

        return $this->getFieldsBySchema($this->accessSchema);
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionSummary()
    {
        $result = [];

        if (!$this->isRegisterMode()) {
            if (empty($this->summarySchema['referer'][static::SCHEMA_ATTRIBUTES])) {
                $this->summarySchema['referer'][static::SCHEMA_ATTRIBUTES] = [];
            }

            $this->summarySchema['referer'][static::SCHEMA_ATTRIBUTES]['title'] = $this->getDefaultFieldValue('referer');

            $result = $this->getFieldsBySchema($this->summarySchema);
        }

        return $result;
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
        if ('access_level' === $name) {
            $field = $this->getFormField('access', 'access_level');
            if ($field && $field instanceof \XLite\View\FormField\Label) {
                $value = $this->getAccessLevelAsText();
            }
        }

        return isset($value) ? $value : parent::getModelObjectValue($name);
    }

    /**
     * Get access level as text
     *
     * @return string
     */
    protected function getAccessLevelAsText()
    {
        if (\XLite\Core\Auth::getInstance()->getAdminAccessLevel() <= $this->getModelObject()->getAccessLevel()) {
            $label = static::t('Administrator');

        } elseif ($this->getModelObject()->getAnonymous()) {
            $sameProfile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                ->findUserWithSameLogin($this->getModelObject());
            if ($sameProfile) {
                $label = static::t(
                    'Anonymous Customer, _Registered User with the same email_',
                    [
                        'URL' => static::buildURL('profile', '', ['profile_id' => $sameProfile->getProfileId()]),
                    ]
                );

            } else {
                $label = static::t('Anonymous Customer');
            }

        } else {
            $sameProfile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findOneBy([
                'login'     => $this->getModelObject()->getLogin(),
                'order'     => null,
                'anonymous' => true,
            ]);
            if ($sameProfile) {
                $label = static::t(
                    'Registered Customer, _Anonymous Customer with the same email_',
                    [
                        'URL' => static::buildURL('profile', '', ['profile_id' => $sameProfile->getProfileId()]),
                    ]
                );

            } else {
                $label = static::t('Registered Customer');
            }
        }

        return $label;
    }

    /**
     * TRUE if the user edits own profile
     *
     * @return boolean
     */
    protected function isLoggedProfile()
    {
        $request = \XLite\Core\Request::getInstance();

        return !$request->profile_id
               || \XLite\Core\Auth::getInstance()->getProfile()->getProfileId() == $request->profile_id;
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
        $adminAccessLevel = \XLite\Core\Auth::getInstance()->getAdminAccessLevel();

        if (!empty($data['password'])) {
            // Encrypt password if if is not empty
            $data['password'] = \XLite\Core\Auth::encryptPassword($data['password']);
        } elseif (isset($data['password'])) {
            // Otherwise unset password to avoid passing empty password to the database
            unset($data['password']);
        }

        // Cannot change the status of own profile
        if ($this->isLoggedProfile()) {
            unset($data['status']);
        }

        // Apply the access level only during the profile creation
        if (!$this->isRegisterMode()) {
            unset($data['access_level']);
        }

        if (isset($data['forceChangePassword']) && is_string($data['forceChangePassword'])) {
            $data['forceChangePassword'] = (bool)$data['forceChangePassword'];
        }

        $isRoot = \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);

        if (
            isset($data['roles'])
            && (!$isRoot || (isset($data['access_level']) && $adminAccessLevel != $data['access_level']))
        ) {
            unset($data['roles']);
        }

        /** @var \XLite\Model\Profile $model */
        $model = $this->getModelObject();

        // Assign only role for admin
        $isAdmin = (isset($data['access_level']) && $adminAccessLevel == $data['access_level'])
                   || ($model->getProfileId() && $model->isAdmin());

        if ($isAdmin && $this->needSetRootAccess($this->getModelObject())) {
            $rootRole = \XLite\Core\Database::getRepo('XLite\Model\Role')->findOneRoot();
            if ($rootRole) {
                if (!isset($data['roles'])) {
                    $data['roles'] = [];
                }

                $data['roles'][] = $rootRole->getId();
            }
        }

        if ($isAdmin && !isset($data['roles']) && $isRoot && !$this->isLoggedProfile()) {
            $data['roles'] = [];
        }

        if (isset($data['roles'])
            || (isset($data['access_level']) && $adminAccessLevel != $data['access_level'])
            || ($model->getProfileId() && !$model->isAdmin())
        ) {
            // Remove old links
            foreach ($model->getRoles() as $role) {
                $role->getProfiles()->removeElement($model);
            }
            $model->getRoles()->clear();
        }

        // Add new links
        if (isset($data['roles']) && is_array($data['roles'])) {
            $data['roles'] = array_unique($data['roles']);
            foreach ($data['roles'] as $rid) {
                $role = \XLite\Core\Database::getRepo('XLite\Model\Role')->find($rid);
                if ($role) {
                    $model->addRoles($role);
                    $role->addProfiles($model);
                }
            }
        }

        if (isset($data['roles'])) {
            unset($data['roles']);
        }

        if (isset($data['login']) && $data['login'] !== $model->getLogin() && $model->getOrdersCount()) {
            \XLite\Core\Database::getRepo('XLite\Model\Profile')->updateOrderProfileEmailByOrigProfile($model, $data['login']);
        }

        parent::setModelProperties($data);
    }

    /**
     * Rollback model if data validation failed
     *
     * @return void
     */
    protected function rollbackModel()
    {
        $roles = $this->getModelObject()->getRoles();
        foreach ($roles as $role) {
            \XLite\Core\Database::getEM()->refresh($role);
        }

        parent::rollbackModel();
    }

    /**
     * Check - need set root access or not
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return boolean
     */
    protected function needSetRootAccess(\XLite\Model\Profile $profile)
    {
        $onlyOneRootAdmin = false;

        if ($profile->getProfileId()) {
            $cnd = new \XLite\Core\CommonCell;
            $cnd->permissions = \XLite\Model\Role\Permission::ROOT_ACCESS;
            $i = 0;
            foreach (\XLite\Core\Database::getRepo('XLite\Model\Profile')->search($cnd) as $p) {
                $i++;
                if ($profile->getProfileId() == $p->getProfileId()) {
                    $onlyOneRootAdmin = true;
                }
            }

            if (1 < $i) {
                $onlyOneRootAdmin = false;
            }
        }

        return 1 == \XLite\Core\Database::getRepo('XLite\Model\Role')->count()
               || $onlyOneRootAdmin;
    }

    /**
     * Prepare request data for mapping profile object
     *
     * @return array
     */
    protected function prepareDataForMapping()
    {
        $data = parent::prepareDataForMapping();

        if (isset($data['membership_id']) && 0 < (int)$data['membership_id']) {
            $membership = \XLite\Core\Database::getRepo('XLite\Model\Membership')->find($data['membership_id']);

            if (isset($membership)) {
                $data['membership'] = $membership;
            }
        }

        if (!isset($data['membership'])) {
            $data['membership'] = null;
        }

        return $data;
    }

    /**
     * Check password and its confirmation
     * TODO: simplify
     *
     * @return boolean
     */
    protected function checkPassword()
    {
        $result = true;
        $data = $this->getRequestData();

        if (isset($this->sections[self::SECTION_MAIN])
            && (!empty($data['password']) || !empty($data['password_conf']))
        ) {
            if ($data['password'] != $data['password_conf']) {
                $result = false;
                $formFields = $this->getFormFields();
                $this->addErrorMessage(
                    'password',
                    'Password and its confirmation do not match',
                    $formFields[self::SECTION_MAIN]
                );
            }

        } else {
            $this->excludeField('password');
            $this->excludeField('password_conf');
        }

        return $result;
    }

    /**
     * Check profile data
     *
     * @return boolean
     */
    protected function checkProfileData()
    {
        $result = $this->checkPassword();

        if ($result) {
            // Check if profile with specified login is already exists
            $sameProfile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                ->findUserWithSameLogin($this->getModelObject());

            if (isset($sameProfile)) {
                $formFields = $this->getFormFields();
                $this->addErrorMessage(
                    'login',
                    'User with specified email is already registered',
                    $formFields[self::SECTION_MAIN]
                );
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Return list of the class-specific sections
     *
     * @return array
     */
    protected function getProfileMainSections()
    {
        return [
            self::SECTION_SUMMARY => 'Account summary',
            self::SECTION_MAIN    => 'Email &amp; password',
            self::SECTION_ACCESS  => 'Access information',
        ];
    }

    /**
     * Return error message for the "validateInput" action
     *
     * @param string $login Profile login
     *
     * @return string
     */
    protected function getErrorActionValidateInputMessage($login)
    {
        return 'The <i>' . $login . '</i> profile is already registered. '
               . 'Please, try some other email address.';
    }

    /**
     * Process the errors occurred during the "validateInput" action
     *
     * @return void
     */
    protected function postprocessErrorActionValidateInput()
    {
        \XLite\Core\TopMessage::addError(
            $this->getErrorActionValidateInputMessage($this->getRequestData('login'))
        );
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionCreate()
    {
        \XLite\Core\TopMessage::addInfo('Profile has been created successfully');
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionUpdate()
    {
        \XLite\Core\TopMessage::addInfo('Profile has been updated successfully');
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionModify()
    {
        \XLite\Core\TopMessage::addInfo('Profile has been modified successfully');
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionDelete()
    {
        \XLite\Core\TopMessage::addInfo('Profile has been deleted successfully');
    }

    /**
     * Create profile
     *
     * @return boolean
     */
    protected function performActionCreate()
    {
        return $this->checkProfileData() ? parent::performActionCreate() : false;
    }

    /**
     * Update profile
     *
     * @return boolean
     */
    protected function performActionUpdate()
    {
        $data = $this->getRequestData();
        $result = $this->checkPassword() ? parent::performActionUpdate() : false;

        if ($result && !empty($data['password']) && $profile = $this->getModelObject()) {
            $profile->logoffSessions(true);
        }

        return $result;
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionDelete()
    {
        return parent::performActionDelete();
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionValidateInput()
    {
        $result = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findUserWithSameLogin($this->getModelObject());

        return $result;
    }

    /**
     * Return text for the "Submit" button
     *
     * @return string
     */
    protected function getSubmitButtonLabel()
    {
        return $this->isRegisterMode() ? 'Create account' : 'Update';
    }

    /**
     * Return text for the "Submit" button
     *
     * @return string
     */
    protected function getSubmitButtonStyle()
    {
        return 'profile-form';
    }

    /**
     * Return class of button panel widget
     *
     * @return string
     */
    protected function getButtonPanelClass()
    {
        $result = parent::getButtonPanelClass();

        if ($this->getModelObject()
            && $this->getModelObject()->isPersistent()
            && $this->getModelObject()->getAnonymous()
            && !$this->getModelObject()->getOrder()
        ) {
            $result = '\XLite\View\StickyPanel\Model\Profile\Anonymous';
        }

        return $result;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        if (!$this->getModelObject() || !$this->getModelObject()->getAnonymous()) {
            $result['submit'] = new \XLite\View\Button\Submit(
                [
                    \XLite\View\Button\AButton::PARAM_LABEL    => $this->getSubmitButtonLabel(),
                    \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                    \XLite\View\Button\AButton::PARAM_STYLE    => $this->getSubmitButtonStyle(),
                ]
            );
        }

        $same = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findUserWithSameLogin($this->getModelObject());

        if ($this->getModelObject()
            && $this->getModelObject()->isPersistent()
            && $this->getModelObject()->getAnonymous()
            && !$this->getModelObject()->getOrder()
        ) {
            if (!$same) {
                $result['register'] = new \XLite\View\Button\Regular(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL      => static::t('Register user'),
                        \XLite\View\Button\AButton::PARAM_BTN_TYPE   => 'regular-main-button',
                        \XLite\View\Button\AButton::PARAM_STYLE      => 'register',
                        \XLite\View\Button\Regular::PARAM_ACTION     => 'registerAsNew',
                        \XLite\View\Button\AButton::PARAM_ATTRIBUTES => [
                            'title' => static::t('The user will be registered; a password will be sent to the user via email'),
                        ],
                    ]
                );

            } elseif ($same && !$same->isAdmin()) {
                $result['merge'] = new \XLite\View\Button\Regular(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL  => static::t('Merge with Registered'),
                        \XLite\View\Button\AButton::PARAM_STYLE  => 'merge',
                        \XLite\View\Button\Regular::PARAM_ACTION => 'mergeWithRegistered',
                    ]
                );
            }
        }

        return $result;
    }
}
