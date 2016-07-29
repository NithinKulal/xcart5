<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment;

/**
 * Payment method
 *
 * @Entity
 * @Table  (name="payment_methods",
 *      indexes={
 *          @Index (name="orderby", columns={"orderby"}),
 *          @Index (name="class", columns={"class","enabled"}),
 *          @Index (name="enabled", columns={"enabled"})
 *      }
 * )
 */
class Method extends \XLite\Model\Base\I18n
{
    /**
     * Type codes
     */
    const TYPE_ALLINONE    = 'A';
    const TYPE_CC_GATEWAY  = 'C';
    const TYPE_ALTERNATIVE = 'N';
    const TYPE_OFFLINE     = 'O';


    /**
     * Payment method unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $method_id;

    /**
     * Method service name (gateway or API name)
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $service_name;

    /**
     * Process class name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $class;

    /**
     * Specific module family name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $moduleName = '';

    /**
     * Position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $orderby = 0;

    /**
     * Position in popup
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $adminOrderby = 0;

    /**
     * Enabled status
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = false;

    /**
     * Module enabled status
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $moduleEnabled = true;

    /**
     * Added status
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $added = false;

    /**
     * Type
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $type = self::TYPE_OFFLINE;

    /**
     * Settings
     *
     * @var \XLite\Model\Payment\MethodSetting
     *
     * @OneToMany (targetEntity="XLite\Model\Payment\MethodSetting", mappedBy="payment_method", cascade={"all"})
     */
    protected $settings;

    /**
     * Flag:
     *   1 - method has been got from marketplace,
     *   0 - method has been added after distr or module installation
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $fromMarketplace = false;

    /**
     * Payment method icon URL (used for methods from marketplace)
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $iconURL;

    /**
     * Countries of merchant (merchants from these countries can sign up and use this method)
     *
     * @var array
     *
     * @Column (type="array", nullable=true)
     */
    protected $countries;

    /**
     * Excluded countries (merchants from these countries cannot sign up for payment account)
     *
     * @var array
     *
     * @Column (type="array", nullable=true)
     */
    protected $exCountries;


    /**
     * Get processor
     *
     * @return \XLite\Model\Payment\Base\Processor
     */
    public function getProcessor()
    {
        $class = '\XLite\\' . $this->getClass();

        return \XLite\Core\Operator::isClassExists($class) ? $class::getInstance() : null;
    }

    /**
     * Check - enabled method or not
     * FIXME - must be removed
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return ($this->getEnabled() || $this->isForcedEnabled())
            && $this->getAdded()
            && $this->getModuleEnabled()
            && $this->getProcessor()
            && $this->getProcessor()->isConfigured($this);
    }

    /**
     * Set class
     *
     * @return void
     */
    public function setClass($class)
    {
        $this->class = preg_replace('/^\\\?(?:XLite\\\)?\\\?/Sis', '', $class);

        if (preg_match('/^Module/Sis', $class) > 0) {

            list($modulePrefix, $author, $name) = explode('\\', $class, 4);

            $this->setModuleName($author . '_' . $name);
        }
    }

    /**
     * Get setting value by name
     *
     * @param string $name Name
     *
     * @return string|void
     */
    public function getSetting($name)
    {
        $entity = $this->getSettingEntity($name);

        return $entity ? $entity->getValue() : null;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->getOrderby();
    }

    /**
     * Set position
     *
     * @param integer $position Position
     *
     * @return integer
     */
    public function setPosition($position)
    {
        return $this->setOrderby($position);
    }

    /**
     * Get setting by name
     *
     * @param string $name Name
     *
     * @return \XLite\Model\Payment\MethodSetting
     */
    public function getSettingEntity($name)
    {
        $result = null;

        foreach ($this->getSettings() as $setting) {
            if ($setting->getName() == $name) {
                $result = $setting;
                break;
            }
        }

        return $result;
    }

    /**
     * Set setting value by name
     *
     * @param string $name  Name
     * @param string $value Value
     *
     * @return boolean
     */
    public function setSetting($name, $value)
    {
        $result = false;

        // Update settings which is already stored in database
        $setting = $this->getSettingEntity($name);

        if ($setting) {
            $setting->setValue(strval($value));
            $result = true;

        } else {

            // Create setting which is not in database but specified in the processor class

            $processor = $this->getProcessor();

            if ($processor && method_exists($processor, 'getAvailableSettings')) {
                $availableSettings = $processor->getAvailableSettings();

                if (in_array($name, $availableSettings)) {
                    $setting = new \XLite\Model\Payment\MethodSetting();
                    $setting->setName($name);
                    $setting->setValue(strval($value));
                    $setting->setPaymentMethod($this);
                    $this->addSettings($setting);

                    \XLite\Core\Database::getEM()->persist($setting);
                }
            }
        }

        return $result;
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->settings     = new \Doctrine\Common\Collections\ArrayCollection();
        $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Call processor methods
     *
     * @param string $method    Method name
     * @param array  $arguments Arguments OPTIONAL
     *
     * @return mixed
     */
    public function __call($method, array $arguments = array())
    {
        if (parent::hasMagicMethod($method)) {
            return parent::__call($method, $arguments);
        }

        array_unshift($arguments, $this);

        return $this->getProcessor()
            ? call_user_func_array(array($this->getProcessor(), $method), $arguments)
            : null;
    }

    /**
     * Get warning note
     *
     * @return string
     */
    public function getWarningNote()
    {
        $message = null;

        if ($this->getProcessor() && !$this->getProcessor()->isConfigured($this)) {
            $message = static::t('The method is not configured and cannot be used');
        }

        if (!$message) {
            $message = $this->getProcessor() ? $this->getProcessor()->getWarningNote($this) : null;
        }

        return $message;
    }

    /**
     * Get payment method admin zone icon URL
     *
     * @return string
     */
    public function getAdminIconURL()
    {
        $url = $this->getProcessor() ? $this->getProcessor()->getAdminIconURL($this) : null;

        if (true === $url) {
            $module = $this->getProcessor()->getModule();
            $url = $module
                ? \XLite\Core\Layout::getInstance()->getResourceWebPath('modules/' . $module->getAuthor() . '/' . $module->getName() . '/method_icon.png')
                : null;
        }

        return $url;
    }

    /**
     * Get payment method alternative admin zone icon URL
     *
     * @return string
     */
    public function getAltAdminIconURL()
    {
        $module = $this->getProcessor()->getModule();

        return $module
            ? \XLite\Core\Layout::getInstance()->getResourceWebPath(
                'modules/' . $module->getAuthor() . '/' . $module->getName() . '/method_icon_' . $this->getAdaptedServiceName() . '.png'
            )
            : null;
    }

    /**
     * Get adapted service name (e.g. 'Sage Pay form protocol' will be converted to 'Sage_Pay_form_protocol')
     *
     * @return string
     */
    public function getAdaptedServiceName()
    {
        return preg_replace('/_+/', '_', preg_replace('/[^\w\d]+/', '_', $this->getServiceName()));
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled Property value
     *
     * @return \XLite\Model\Payment\Method
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        if ($this->getProcessor()) {
            $this->getProcessor()->enableMethod($this);
        }

        return $this;
    }

    /**
     * Translation getter
     *
     * @return string
     */
    public function getDescription()
    {
        $description = $this->getSoftTranslation()->getDescription();

        if (\XLite\Core\Auth::getInstance()->isOperatingAsUserMode()) {
            $methods = \XLite\Core\Auth::getInstance()->getOperateAsUserPaymentMethods();

            $currentServiceName = $this->getServiceName();

            $found = array_reduce(
                $methods,
                function($carry, $method) use ($currentServiceName){
                    return $carry ?: $method->getServiceName() === $currentServiceName;
                },
                false
            );

            if ($found && !$this->isEnabled()) {
                $description = static::t('This method is displayed because you are logged in as admin and operating as another user');
            }
        }

        return $description;
    }

    /**
     * Set 'added' property
     *
     * @param boolean $added Property value
     *
     * @return \XLite\Model\Payment\Method
     */
    public function setAdded($added)
    {
        $this->added = $added;

        if (!$added) {
            $this->setEnabled(false);
        }

        return $this;
    }

    /**
     * Return true if payment module is installed
     *
     * @return boolean
     */
    public function isModuleInstalled()
    {
        $result = true;
        $moduleName = $this->getModuleName();

        if ($moduleName) {
            $moduleName = str_replace('_', '\\', $moduleName);
            $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->findOneByModuleName($moduleName);
            $result = !empty($module);
        }

        return $result;
    }

    /**
     * Get payment module
     *
     * @return \XLite\Model\Module|null
     */
    public function getModule()
    {
        $module = null;
        $moduleName = $this->getModuleName();

        if ($moduleName) {
            $moduleName = str_replace('_', '\\', $moduleName);
            $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->findModuleByName($moduleName);
        }

        return $module;
    }

    /**
     * Get message why we can't switch payment method
     *
     * @return string
     */
    public function getNotSwitchableReason()
    {
        return static::t('This payment method is not configured.');
    }

    /**
     * Get payment module ID
     *
     * @return integer|null
     */
    public function getModuleId()
    {
        return $this->getModule() ? $this->getModule()->getModuleId() : null;
    }

    /**
     * Get method_id
     *
     * @return integer 
     */
    public function getMethodId()
    {
        return $this->method_id;
    }

    /**
     * Set service_name
     *
     * @param string $serviceName
     * @return Method
     */
    public function setServiceName($serviceName)
    {
        $this->service_name = $serviceName;
        return $this;
    }

    /**
     * Get service_name
     *
     * @return string 
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set moduleName
     *
     * @param string $moduleName
     * @return Method
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    /**
     * Get moduleName
     *
     * @return string 
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Set orderby
     *
     * @param integer $orderby
     * @return Method
     */
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;
        return $this;
    }

    /**
     * Get orderby
     *
     * @return integer 
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * Set adminOrderby
     *
     * @param integer $adminOrderby
     * @return Method
     */
    public function setAdminOrderby($adminOrderby)
    {
        $this->adminOrderby = $adminOrderby;
        return $this;
    }

    /**
     * Get adminOrderby
     *
     * @return integer 
     */
    public function getAdminOrderby()
    {
        return $this->adminOrderby;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set moduleEnabled
     *
     * @param boolean $moduleEnabled
     * @return Method
     */
    public function setModuleEnabled($moduleEnabled)
    {
        $this->moduleEnabled = $moduleEnabled;
        return $this;
    }

    /**
     * Get moduleEnabled
     *
     * @return boolean 
     */
    public function getModuleEnabled()
    {
        return $this->moduleEnabled;
    }

    /**
     * Get added
     *
     * @return boolean 
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Method
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set fromMarketplace
     *
     * @param boolean $fromMarketplace
     * @return Method
     */
    public function setFromMarketplace($fromMarketplace)
    {
        $this->fromMarketplace = $fromMarketplace;
        return $this;
    }

    /**
     * Get fromMarketplace
     *
     * @return boolean 
     */
    public function getFromMarketplace()
    {
        return $this->fromMarketplace;
    }

    /**
     * Set iconURL
     *
     * @param string $iconURL
     * @return Method
     */
    public function setIconURL($iconURL)
    {
        $this->iconURL = $iconURL;
        return $this;
    }

    /**
     * Get iconURL
     *
     * @return string 
     */
    public function getIconURL()
    {
        return $this->iconURL;
    }

    /**
     * Set countries
     *
     * @param array $countries
     * @return Method
     */
    public function setCountries($countries)
    {
        $this->countries = $countries;
        return $this;
    }

    /**
     * Get countries
     *
     * @return array 
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * Set exCountries
     *
     * @param array $exCountries
     * @return Method
     */
    public function setExCountries($exCountries)
    {
        $this->exCountries = $exCountries;
        return $this;
    }

    /**
     * Get exCountries
     *
     * @return array 
     */
    public function getExCountries()
    {
        return $this->exCountries;
    }

    /**
     * Add settings
     *
     * @param \XLite\Model\Payment\MethodSetting $settings
     * @return Method
     */
    public function addSettings(\XLite\Model\Payment\MethodSetting $settings)
    {
        $this->settings[] = $settings;
        return $this;
    }

    /**
     * Get settings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSettings()
    {
        return $this->settings;
    }
}
