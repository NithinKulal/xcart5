<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * The "profile" model class
 *
 * @Entity
 * @Table  (name="profiles",
 *      indexes={
 *          @Index (name="cms_profile", columns={"cms_name","cms_profile_id"}),
 *          @Index (name="login", columns={"login"}),
 *          @Index (name="order_id", columns={"order_id"}),
 *          @Index (name="password", columns={"password"}),
 *          @Index (name="access_level", columns={"access_level"}),
 *          @Index (name="first_login", columns={"first_login"}),
 *          @Index (name="last_login", columns={"last_login"}),
 *          @Index (name="status", columns={"status"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class Profile extends \XLite\Model\AEntity
{
    /**
     * Status codes
     */
    const STATUS_ENABLED  = 'E';
    const STATUS_DISABLED = 'D';

    /**
     * Merge flags
     */
    const MERGE_ALL       = 3;
    const MERGE_ADDRESSES = 1;
    const MERGE_ORDERS    = 2;


    /**
     * Profile unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $profile_id;

    /**
     * Login (e-mail)
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $login = '';

    /**
     * Password
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $password = '';

    /**
     * Password hint
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $password_hint = '';

    /**
     * Password hint answer
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $password_hint_answer = '';

    /**
     * Password reset key (for 'Forgot password')
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $passwordResetKey = '';

    /**
     * Timestamp of reset key creation date
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $passwordResetKeyDate = 0;

    /**
     * Access level
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $access_level = 0;

    /**
     * CMS profile Id
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $cms_profile_id = 0;

    /**
     * CMS name
     *
     * @var string
     *
     * @Column (type="string", length=32)
     */
    protected $cms_name = '';

    /**
     * Timestamp of profile creation date
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $added = 0;

    /**
     * Timestamp of first login event
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $first_login = 0;

    /**
     * Timestamp of last login event
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $last_login = 0;

    /**
     * Profile status
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $status = self::STATUS_ENABLED;

    /**
     * Status comment (reason)
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $statusComment = '';

    /**
     * Referer
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $referer = '';

    /**
     * Relation to a order
     *
     * @var \XLite\Model\Order
     *
     * @OneToOne   (targetEntity="XLite\Model\Order")
     * @JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Relation to an event
     *
     * @var \XLite\Model\OrderHistoryEvents
     *
     * @OneToMany   (targetEntity="XLite\Model\OrderHistoryEvents", mappedBy="author")
     * @JoinColumn (name="event_id", referencedColumnName="event_id", onDelete="CASCADE")
     */
    protected $event;

    /**
     * Language code
     *
     * @var string
     *
     * @Column (type="string", length=2)
     */
    protected $language = '';

    /**
     * Last selected shipping id
     *
     * @var integer
     *
     * @Column (type="integer", nullable=true)
     */
    protected $last_shipping_id;

    /**
     * Last selected payment id
     *
     * @var integer
     *
     * @Column (type="integer", nullable=true)
     */
    protected $last_payment_id;

    /**
     * Membership: many-to-one relation with memberships table
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToOne  (targetEntity="XLite\Model\Membership")
     * @JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="SET NULL")
     */
    protected $membership;

    /**
     * Pending membership: many-to-one relation with memberships table
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToOne  (targetEntity="XLite\Model\Membership")
     * @JoinColumn (name="pending_membership_id", referencedColumnName="membership_id", onDelete="SET NULL")
     */
    protected $pending_membership;

    /**
     * Address book: one-to-many relation with address book entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="XLite\Model\Address", mappedBy="profile", cascade={"all"})
     */
    protected $addresses;

    /**
     * Roles
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany (targetEntity="XLite\Model\Role", mappedBy="profiles", cascade={"merge","detach","persist"})
     */
    protected $roles;

    /**
     * The count of orders placed by the user
     *
     * @var integer
     */
    protected $orders_count = null;

    /**
     * Flag of anonymous profile (used for checkout process only)
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $anonymous = false;

    /**
     * Flag if the user needs to change the password.
     * The customers only
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $forceChangePassword = false;

    /**
     * Date of last login attempt
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $dateOfLoginAttempt = 0;

    /**
     * Count of login attempt
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $countOfLoginAttempts = 0;

    /**
     * Fake field for search
     *
     * @var string
     * 
     * @Column (type="text", nullable=true)
     */
    protected $searchFakeField;

    /**
     * Flag to exporting entities
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $xcPendingExport = false;

    /**
     * Set referer
     *
     * @param string $value Value
     *
     * @return void
     */
    public function setReferer($value)
    {
        $this->referer = substr($value, 0, 255);
    }

    /**
     * Set membership
     *
     * @param \XLite\Model\Membership $membership Membership OPTIONAL
     *
     * @return void
     */
    public function setMembership(\XLite\Model\Membership $membership = null)
    {
        $this->membership = $membership;
    }

    /**
     * Set pending membership
     *
     * @param \XLite\Model\Membership $pendingMembership Pending membership OPTIONAL
     *
     * @return void
     */
    public function setPendingMembership(\XLite\Model\Membership $pendingMembership = null)
    {
        $this->pending_membership = $pendingMembership;
    }

    /**
     * Get membership Id
     *
     * @return integer
     */
    public function getMembershipId()
    {
        return $this->getMembership() ? $this->getMembership()->getMembershipId() : null;
    }

    /**
     * Get pending membership Id
     *
     * @return integer
     */
    public function getPendingMembershipId()
    {
        return $this->getPendingMembership() ? $this->getPendingMembership()->getMembershipId() : null;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName($useDefault = true)
    {
        $address = $this->getBillingAddress() ?: $this->getShippingAddress();

        return $address && ($address->getFirstname() || $address->getLastname())
            ? trim($address->getFirstname() . ' ' . $address->getLastname())
            : ($useDefault ? $this->getDefaultName() : '');
    }

    /**
     * Get default name
     *
     * @return string
     */
    protected function getDefaultName()
    {
        return $this->isAdmin()
            ? static::t('na_admin')
            : static::t('na_customer');
    }

    /**
     * Returns billing address
     *
     * @return \XLite\Model\Address
     */
    public function getBillingAddress()
    {
        return $this->getAddressByType(\XLite\Model\Address::BILLING);
    }

    /**
     * Returns shipping address
     *
     * @return \XLite\Model\Address
     */
    public function getShippingAddress()
    {
        return $this->getAddressByType(\XLite\Model\Address::SHIPPING);
    }


    /**
     * Switches current billing address to a new one
     * 
     * @param \XLite\Model\Address $new
     */
    public function setBillingAddress($new)
    {
        $current = $this->getBillingAddress();

        if ($current && $current->getUniqueIdentifier() == $new->getUniqueIdentifier()) {
            return;
        }

        $this->setAddress('billing', $new);
    }

    /**
     * Switches current shipping address to a new one
     * 
     * @param \XLite\Model\Address $new
     */
    public function setShippingAddress($new)
    {
        $current = $this->getShippingAddress();

        if ($current && $current->getUniqueIdentifier() == $new->getUniqueIdentifier()) {
            return;
        }

        $this->setAddress('shipping', $new);
    }

    /**
     * Set current address by type
     * 
     * @param string $type    
     * @param \XLite\Model\Address $new  
     */
    protected function setAddress($type, $new)
    {
        $current = ($type == 'shipping')
            ? $this->getShippingAddress()
            : $this->getBillingAddress();

        if ($current && $current->getUniqueIdentifier() == $new->getUniqueIdentifier()) {
            return;
        }

        $useAsOtherType = isset(\XLite\Core\Session::getInstance()->same_address)
            ? \XLite\Core\Session::getInstance()->same_address
            : null;

        // Disable current address
        if ($current) {
            if ($current->getIsWork()) {                
                $this->getAddresses()->removeElement($current);
                \XLite\Core\Database::getEM()->remove($current);
            }

            $useAsOtherType = ($type == 'shipping') 
                ? $current->getIsBilling() 
                : $current->getIsShipping();

            $current->setIsShipping(false);
            $current->setIsBilling(false);
        }

        // Check if new address is not assigned to this profile
        $addToProfile = true;

        foreach ($this->getAddresses() as $profileAddress) {
            if ($profileAddress->getUniqueIdentifier() == $new->getUniqueIdentifier()) {
                $addToProfile = false;
            }
        }

        if ($addToProfile) {
            $this->addAddresses($new);
            $new->setProfile($this);
        }

        if ($type == 'shipping') {
            $new->setIsShipping(true);
            if ($useAsOtherType !== null) {
                $new->setIsBilling($useAsOtherType);
            }
        } else {
            $new->setIsBilling(true);
            if ($useAsOtherType !== null) {
                $new->setIsShipping($useAsOtherType);
            }
        }
    }

    /**
     * Returns first available address
     *
     * @return \XLite\Model\Address
     */
    public function getFirstAddress()
    {
        $result = null;

        foreach ($this->getAddresses() as $address) {
            $result = $address;
            break;
        }

        return $result;
    }

    /**
     * Has tax exemption
     *
     * @return boolean
     */
    public function hasTaxExemption()
    {
        return false;
    }

    /**
     * Returns the number of orders places by the user
     *
     * @return integer
     */
    public function getOrdersCount()
    {
        if (null === $this->orders_count) {
            $cnd = new \XLite\Core\CommonCell();
            $cnd->profile = $this;

            $this->orders_count = \XLite\Core\Database::getRepo('XLite\Model\Order')->search($cnd, true);
        }

        return $this->orders_count;
    }

    /**
     * Check if profile is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return static::STATUS_ENABLED === strtoupper($this->getStatus());
    }

    /**
     * Enable user profile
     *
     * @return void
     */
    public function enable()
    {
        $this->setStatus(static::STATUS_ENABLED);
    }

    /**
     * Disable user profile
     *
     * @return void
     */
    public function disable()
    {
        $this->setStatus(static::STATUS_DISABLED);
    }

    /**
     * Returns true if profile has an administrator access level
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->getAccessLevel() >= \XLite\Core\Auth::getInstance()->getAdminAccessLevel();
    }

    /**
     * Create an entity profile in the database
     *
     * @return boolean
     */
    public function create()
    {
        $this->prepareCreate();

        return parent::create();
    }

    /**
     * Update an entity in the database
     *
     * @param boolean $cloneMode Clone mode OPTIONAL
     *
     * @return boolean
     */
    public function update($cloneMode = false)
    {
        // Check if user with specified e-mail address is already exists
        $exists = $cloneMode
            ? null
            : \XLite\Core\Database::getRepo('XLite\Model\Profile')->checkRegisteredUserWithSameLogin($this);

        if ($exists) {
            $this->addErrorEmailExists();
            $result = false;

        } else {

            $this->updateSearchFakeField();
            // Do an entity update
            $result = parent::update();
        }

        return $result;
    }

    /**
     * Delete an entity profile from the database
     *
     * @return boolean
     */
    public function delete()
    {
        // Check if the deleted profile is a last admin profile
        if ($this->isAdmin() && 1 == \XLite\Core\Database::getRepo('XLite\Model\Profile')->findCountOfAdminAccounts()) {
            $result = false;

            \XLite\Core\TopMessage::addError('The only remaining active administrator profile cannot be deleted.');

        } else {
            $result = parent::delete();
        }

        return $result;
    }

    /**
     * Check if billing and shipping addresses are equal or not
     * TODO: review method after implementing at one-step-checkout
     *
     * @return boolean
     */
    public function isSameAddress()
    {
        $result = false;

        $billingAddress = $this->getBillingAddress();
        $shippingAddress = $this->getShippingAddress();

        if (null !== $billingAddress && null !== $shippingAddress) {
            $result = true;

            if ($billingAddress->getAddressId() != $shippingAddress->getAddressId()) {
                $addressFields = $billingAddress->getAvailableAddressFields();

                foreach ($addressFields as $name) {
                    $methodName = 'get' . \XLite\Core\Converter::getInstance()->convertToCamelCase($name);

                    // Compare field values of billing and shipping addresses
                    if ($billingAddress->$methodName() != $shippingAddress->$methodName()) {
                        $result = false;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Check - billing and shipping addresses are equal or not
     *
     * @param boolean $strict Flag: true - both billing and shipping addresses must be defined OPTIONAL
     *
     * @return boolean
     */
    public function isEqualAddress($strict = false)
    {
        $billingAddress = $this->getBillingAddress();
        $shippingAddress = $this->getShippingAddress();

        $result = null !== $billingAddress && null !== $shippingAddress;

        return $strict
            ? $result && $billingAddress->getAddressId() == $shippingAddress->getAddressId()
            : !$result || $billingAddress->getAddressId() == $shippingAddress->getAddressId();
    }

    /**
     * Clone
     *
     * @return \XLite\Model\Profile
     */
    public function cloneEntity()
    {
        $newProfile = parent::cloneEntity();

        if (!$newProfile->update(true) || !$newProfile->getProfileId()) {
            // TODO - add throw exception
            \XLite::getInstance()->doGlobalDie('Can not clone profile');
        }

        $newProfile->setMembership($this->getMembership());
        $newProfile->setPendingMembership($this->getPendingMembership());
        $newProfile->setPassword('');

        $billingAddress = $this->getBillingAddress();
        if (null !== $billingAddress) {
            $newBillingAddress = $billingAddress->cloneEntity();
            $newBillingAddress->setProfile($newProfile);
            $newProfile->addAddresses($newBillingAddress);
            $newBillingAddress->update();
        }

        $shippingAddress = $this->getShippingAddress();
        if ($shippingAddress
            && (!$billingAddress || $billingAddress->getAddressId() != $shippingAddress->getAddressId())
        ) {
            $newShippingAddress = $shippingAddress->cloneEntity();
            $newShippingAddress->setProfile($newProfile);
            $newProfile->addAddresses($newShippingAddress);
            $newShippingAddress->update();
        }

        $newProfile->update(true);

        return $newProfile;
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->addresses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles     = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get language code
     *
     * @param boolean $flagCreate Flag create OPTIONAL
     *
     * @return string
     */
    public function getLanguage($flagCreate = false)
    {
        return $flagCreate
            ? $this->getLanguageForCreateProfile()
            : $this->checkForActiveLanguage($this->language);
    }

    /**
     * Define the language code for created profile
     *
     * @return string
     */
    protected function getLanguageForCreateProfile()
    {
        return '';
    }

    /**
     * Check if the language code is in the active languages list
     * If customer language is not used right now, the default customer language code is used
     *
     * @param string $languageCode Language code
     *
     * @return string
     */
    protected function checkForActiveLanguage($languageCode)
    {
        $result = $languageCode;
        $langs = \XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages();

        if (!empty($langs)) {
            $resultModel = \Includes\Utils\ArrayManager::searchInObjectsArray(
                $langs,
                'getCode',
                $result
            );

            if (null === $resultModel) {
                $result = \XLite\Core\Config::getInstance()->General->default_language;
            }
        }

        return $result;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order Order OPTIONAL
     *
     * @return void
     */
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;
    }

    /**
     * Get password hash algorithm
     *
     * @return string
     */
    public function getPasswordAlgo()
    {
        $parts = explode(':', $this->getPassword(), 2);

        return 1 === count($parts) ? 'MD5' : $parts[0];
    }

    /**
     * Merge profile with another profile
     *
     * @param \XLite\Model\Profile $profile Profile
     * @param integer              $flag    Peration flag OPTIONAL
     *
     * @return integer
     */
    public function mergeWithProfile(\XLite\Model\Profile $profile, $flag = self::MERGE_ALL)
    {
        $result = 0;

        // Addresses
        if ($flag & static::MERGE_ADDRESSES) {
            foreach ($profile->getAddresses() as $address) {
                $found = false;
                foreach ($this->getAddresses() as $a) {
                    if ($a->isEqualAddress($address)) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $address = $address->cloneEntity();
                    $this->addAddresses($address);
                    $address->setProfile($this);
                }
            }
            $result |= static::MERGE_ADDRESSES;
        }

        // Orders
        if ($flag & static::MERGE_ORDERS) {
            $cnd = new \XLite\Core\CommonCell;
            $cnd->profile = $profile;
            foreach (\XLite\Core\Database::getRepo('XLite\Model\Order')->search($cnd) as $order) {
                $order->setOrigProfile($this);
            }
            $result |= static::MERGE_ORDERS;
        }

        return $result;
    }

    /**
     * Prepare creation date
     *
     * @return void
     *
     * @PrePersist
     */
    public function prepareBeforeCreate()
    {
        // Assign a profile creation date/time
        if (!$this->getAdded()) {
            $this->setAdded(\XLite\Core\Converter::time());
        }

        // Assign current language
        $language = $this->getLanguage(true);

        if (empty($language)
            && ($this->isPersistent() || !$this->language)
        ) {
            $this->setLanguage(\XLite\Core\Session::getInstance()->getLanguage()->getCode());
        }

        // Assign referer value
        if (empty($this->referer)) {
            if (\XLite\Core\Auth::getInstance()->isAdmin()) {
                $currentlyLoggedInProfile = \XLite\Core\Auth::getInstance()->getProfile();
                $this->setReferer(sprintf('Created by administrator (%s)', $currentlyLoggedInProfile->getLogin()));

            } elseif (isset($_COOKIE[\XLite\Core\Session::LC_REFERER_COOKIE_NAME])) {
                $this->setReferer($_COOKIE[\XLite\Core\Session::LC_REFERER_COOKIE_NAME]);
            }
        }

        $this->updateSearchFakeField();

        // Assign status 'Enabled' if not defined
        if (empty($this->status)) {
            $this->enable();
        }
    }

    /**
     * Prepare object for its creation in the database
     *
     * @return void
     */
    protected function prepareCreate()
    {
    }

    /**
     * Prepare creation date
     *
     * @return void
     *
     * @PreUpdate
     */
    public function prepareBeforeUpdate()
    {
        if ($this->getPendingMembershipId() && $this->getPendingMembershipId() === $this->getMembershipId()) {
            $this->setPendingMembership(null);
        }

        $this->updateSearchFakeField();
    }

    /**
     * Update field for search optimization
     *
     * @return void
     */
    public function updateSearchFakeField()
    {
        $searchFakeFieldParts = array();
        foreach ($this->getAddresses() as $address) {
            $searchFakeFieldParts[] = trim($address->getFirstname() . ' ' . $address->getLastname() . ' ' . $address->getFirstname());
        }
        $searchFakeFieldParts[] = $this->getLogin();

        $this->setSearchFakeField(implode(';', $searchFakeFieldParts));
    }

    /**
     * Returns address by its type (shipping or billing)
     *
     * @param string $atype Address type: b - billing, s - shipping OPTIONAL
     *
     * @return \XLite\Model\Address
     */
    protected function getAddressByType($atype = \XLite\Model\Address::BILLING)
    {
        $result = null;

        foreach ($this->getAddresses() as $address) {
            if ((\XLite\Model\Address::BILLING === $atype && $address->getIsBilling())
                || (\XLite\Model\Address::SHIPPING === $atype && $address->getIsShipping())
            ) {
                // Select address if its type is same as a requested type...
                $result = $address;
                break;
            }
        }

        return $result;
    }

    /**
     * Add error top message 'Email already exists...'
     *
     * @return void
     */
    protected function addErrorEmailExists()
    {
        \XLite\Core\TopMessage::addError('This e-mail address is already in use by another user.');
    }

    // {{{ Roles

    /**
     * Check - specified permission is allowed or not
     *
     * @param string $code Permission code
     *
     * @return boolean
     */
    public function isPermissionAllowed($code)
    {
        $result = false;

        if (0 < count($this->getRoles())) {
            foreach ($this->getRoles() as $role) {
                if ($role->isPermissionAllowed($code)) {
                    $result = true;

                    break;
                }
            }

        } elseif (0 === \XLite\Core\Database::getRepo('XLite\Model\Role')->count()) {
            $result = true;
        }

        return $result;
    }

    // }}}

    /**
     * Get profile_id
     *
     * @return integer 
     */
    public function getProfileId()
    {
        return $this->profile_id;
    }

    /**
     * Set login
     *
     * @param string $login
     * @return Profile
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Profile
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password_hint
     *
     * @param string $passwordHint
     * @return Profile
     */
    public function setPasswordHint($passwordHint)
    {
        $this->password_hint = $passwordHint;
        return $this;
    }

    /**
     * Get password_hint
     *
     * @return string 
     */
    public function getPasswordHint()
    {
        return $this->password_hint;
    }

    /**
     * Set password_hint_answer
     *
     * @param string $passwordHintAnswer
     * @return Profile
     */
    public function setPasswordHintAnswer($passwordHintAnswer)
    {
        $this->password_hint_answer = $passwordHintAnswer;
        return $this;
    }

    /**
     * Get password_hint_answer
     *
     * @return string 
     */
    public function getPasswordHintAnswer()
    {
        return $this->password_hint_answer;
    }

    /**
     * Set passwordResetKey
     *
     * @param string $passwordResetKey
     * @return Profile
     */
    public function setPasswordResetKey($passwordResetKey)
    {
        $this->passwordResetKey = $passwordResetKey;
        return $this;
    }

    /**
     * Get passwordResetKey
     *
     * @return string 
     */
    public function getPasswordResetKey()
    {
        return $this->passwordResetKey;
    }

    /**
     * Set passwordResetKeyDate
     *
     * @param integer $passwordResetKeyDate
     * @return Profile
     */
    public function setPasswordResetKeyDate($passwordResetKeyDate)
    {
        $this->passwordResetKeyDate = $passwordResetKeyDate;
        return $this;
    }

    /**
     * Get passwordResetKeyDate
     *
     * @return integer 
     */
    public function getPasswordResetKeyDate()
    {
        return $this->passwordResetKeyDate;
    }

    /**
     * Set access_level
     *
     * @param integer $accessLevel
     * @return Profile
     */
    public function setAccessLevel($accessLevel)
    {
        $this->access_level = $accessLevel;
        return $this;
    }

    /**
     * Get access_level
     *
     * @return integer 
     */
    public function getAccessLevel()
    {
        return $this->access_level;
    }

    /**
     * Set cms_profile_id
     *
     * @param integer $cmsProfileId
     * @return Profile
     */
    public function setCmsProfileId($cmsProfileId)
    {
        $this->cms_profile_id = $cmsProfileId;
        return $this;
    }

    /**
     * Get cms_profile_id
     *
     * @return integer 
     */
    public function getCmsProfileId()
    {
        return $this->cms_profile_id;
    }

    /**
     * Set cms_name
     *
     * @param string $cmsName
     * @return Profile
     */
    public function setCmsName($cmsName)
    {
        $this->cms_name = $cmsName;
        return $this;
    }

    /**
     * Get cms_name
     *
     * @return string 
     */
    public function getCmsName()
    {
        return $this->cms_name;
    }

    /**
     * Set added
     *
     * @param integer $added
     * @return Profile
     */
    public function setAdded($added)
    {
        $this->added = $added;
        return $this;
    }

    /**
     * Get added
     *
     * @return integer 
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set first_login
     *
     * @param integer $firstLogin
     * @return Profile
     */
    public function setFirstLogin($firstLogin)
    {
        $this->first_login = $firstLogin;
        return $this;
    }

    /**
     * Get first_login
     *
     * @return integer 
     */
    public function getFirstLogin()
    {
        return $this->first_login;
    }

    /**
     * Set last_login
     *
     * @param integer $lastLogin
     * @return Profile
     */
    public function setLastLogin($lastLogin)
    {
        $this->last_login = $lastLogin;
        return $this;
    }

    /**
     * Get last_login
     *
     * @return integer 
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Profile
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set statusComment
     *
     * @param string $statusComment
     * @return Profile
     */
    public function setStatusComment($statusComment)
    {
        $this->statusComment = $statusComment;
        return $this;
    }

    /**
     * Get statusComment
     *
     * @return string 
     */
    public function getStatusComment()
    {
        return $this->statusComment;
    }

    /**
     * Get referer
     *
     * @return string 
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return Profile
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Set last_shipping_id
     *
     * @param integer $lastShippingId
     * @return Profile
     */
    public function setLastShippingId($lastShippingId)
    {
        $this->last_shipping_id = $lastShippingId;
        return $this;
    }

    /**
     * Get last_shipping_id
     *
     * @return integer 
     */
    public function getLastShippingId()
    {
        return $this->last_shipping_id;
    }

    /**
     * Set last_payment_id
     *
     * @param integer $lastPaymentId
     * @return Profile
     */
    public function setLastPaymentId($lastPaymentId)
    {
        $this->last_payment_id = $lastPaymentId;
        return $this;
    }

    /**
     * Get last_payment_id
     *
     * @return integer 
     */
    public function getLastPaymentId()
    {
        return $this->last_payment_id;
    }

    /**
     * Set anonymous
     *
     * @param boolean $anonymous
     * @return Profile
     */
    public function setAnonymous($anonymous)
    {
        $this->anonymous = $anonymous;
        return $this;
    }

    /**
     * Get anonymous
     *
     * @return boolean 
     */
    public function getAnonymous()
    {
        return $this->anonymous;
    }

    /**
     * Set forceChangePassword
     *
     * @param boolean $forceChangePassword
     * @return Profile
     */
    public function setForceChangePassword($forceChangePassword)
    {
        $this->forceChangePassword = $forceChangePassword;
        return $this;
    }

    /**
     * Get forceChangePassword
     *
     * @return boolean 
     */
    public function getForceChangePassword()
    {
        return $this->forceChangePassword;
    }

    /**
     * Set dateOfLoginAttempt
     *
     * @param integer $dateOfLoginAttempt
     * @return Profile
     */
    public function setDateOfLoginAttempt($dateOfLoginAttempt)
    {
        $this->dateOfLoginAttempt = $dateOfLoginAttempt;
        return $this;
    }

    /**
     * Get dateOfLoginAttempt
     *
     * @return integer 
     */
    public function getDateOfLoginAttempt()
    {
        return $this->dateOfLoginAttempt;
    }

    /**
     * Set countOfLoginAttempts
     *
     * @param integer $countOfLoginAttempts
     * @return Profile
     */
    public function setCountOfLoginAttempts($countOfLoginAttempts)
    {
        $this->countOfLoginAttempts = $countOfLoginAttempts;
        return $this;
    }

    /**
     * Get countOfLoginAttempts
     *
     * @return integer 
     */
    public function getCountOfLoginAttempts()
    {
        return $this->countOfLoginAttempts;
    }

    /**
     * Set searchFakeField
     *
     * @param string $searchFakeField
     * @return Profile
     */
    public function setSearchFakeField($searchFakeField)
    {
        $this->searchFakeField = $searchFakeField;
        return $this;
    }

    /**
     * Get searchFakeField
     *
     * @return string 
     */
    public function getSearchFakeField()
    {
        return $this->searchFakeField;
    }

    /**
     * Set xcPendingExport
     *
     * @param boolean $xcPendingExport
     * @return Profile
     */
    public function setXcPendingExport($xcPendingExport)
    {
        $this->xcPendingExport = $xcPendingExport;
        return $this;
    }

    /**
     * Get xcPendingExport
     *
     * @return boolean 
     */
    public function getXcPendingExport()
    {
        return $this->xcPendingExport;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Add event
     *
     * @param \XLite\Model\OrderHistoryEvents $event
     * @return Profile
     */
    public function addEvent(\XLite\Model\OrderHistoryEvents $event)
    {
        $this->event[] = $event;
        return $this;
    }

    /**
     * Get event
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Get membership
     *
     * @return \XLite\Model\Membership 
     */
    public function getMembership()
    {
        return $this->membership;
    }

    /**
     * Get pending_membership
     *
     * @return \XLite\Model\Membership 
     */
    public function getPendingMembership()
    {
        return $this->pending_membership;
    }

    /**
     * Add addresses
     *
     * @param \XLite\Model\Address $addresses
     * @return Profile
     */
    public function addAddresses(\XLite\Model\Address $addresses)
    {
        $this->addresses[] = $addresses;
        return $this;
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Add roles
     *
     * @param \XLite\Model\Role $roles
     * @return Profile
     */
    public function addRoles(\XLite\Model\Role $roles)
    {
        $this->roles[] = $roles;
        return $this;
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Logoff profile from sessions
     *
     * @param bool $exceptCurrent
     * @param bool $flush
     */
    public function logoffSessions($exceptCurrent = true, $flush = true)
    {
        $currentSid = $exceptCurrent ? \XLite\Core\Session::getInstance()->getID() : null;
        $sessionCells = \XLite\Core\Database::getRepo('XLite\Model\SessionCell')->findBy([
            'name' => 'profile_id',
            'value' => $this->getProfileId()
        ]);

        foreach ($sessionCells as $sessionCell) {
            $session = $sessionCell->getSession();
            if (!$exceptCurrent || $session->getSid() !== $currentSid) {
                $session->logoff();
            }
        }
        if ($flush) {
            \XLite\Core\Database::getEM()->flush();
        }
    }
}
