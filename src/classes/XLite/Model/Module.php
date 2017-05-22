<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Module
 *
 * @Entity
 * @Table  (name="modules",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="moduleVersion", columns={"author","name","majorVersion","minorVersion","build","fromMarketplace"})
 *      },
 *      indexes={
 *          @Index (name="enabled", columns={"enabled"}),
 *          @Index (name="date", columns={"date"}),
 *          @Index (name="downloads", columns={"downloads"}),
 *          @Index (name="rating", columns={"rating"}),
 *          @Index (name="ani", columns={"author","name","installed"}),
 *          @Index (name="anmm", columns={"author","name","majorVersion","minorVersion"}),
 *          @Index (name="anf", columns={"author","name","fromMarketplace"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class Module extends \XLite\Model\AEntity
{
    /**
     * Value of not X-Cart 5 module plan (zero)
     */
    const NOT_XCN_MODULE = 0;

    /**
     * Value of xcn_plan for non-available modules
     */
    const NOT_AVAILABLE_MODULE = -1;

    /**
     * Identity separator to generate MarketplaceID hash
     */
    const IDENTITY_SEPARATOR = '.';

    /**
     * Module ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $moduleID;

    /**
     * Name
     *
     * @var string
     *
     * @Column (type="string", length=64)
     */
    protected $name;

    /**
     * Author
     *
     * @var string
     *
     * @Column (type="string", length=64)
     */
    protected $author;

    /**
     * Author
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $authorEmail;

    /**
     * Enabled
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = false;

    /**
     * Installed status
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $installed = false;

    /**
     * install.yaml file load status
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $yamlLoaded = false;

    /**
     * Order creation timestamp
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $date = 0;

    /**
     * Rating
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $rating = 0;

    /**
     * Votes
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $votes = 0;

    /**
     * Downloads
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $downloads = 0;

    /**
     * Price
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=2)
     */
    protected $price = 0.00;

    /**
     * Currency code
     *
     * @var string
     *
     * @Column (type="string", length=3)
     */
    protected $currency = 'USD';

    /**
     * Major version
     *
     * @var string
     *
     * @Column (type="string", length=8)
     */
    protected $majorVersion;

    /**
     * Minor version
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $minorVersion;

    /**
     * Version build number (4th digit of version number)
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $build = 0;

    /**
     * Minor core version which is required for module functionality
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $minorRequiredCoreVersion = 0;

    /**
     * Revision date
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $revisionDate = 0;

    /**
     * Module pack size (received from marketplace)
     *
     * @var integer
     *
     * @Column (type="bigint")
     */
    protected $packSize = 0;

    /**
     * X-Cart 5 plan identificator for module
     *  0 - for all kinds of installation
     * -1 - not for downloadable
     * >0 - part of specific X-Cart 5 plan
     *
     * @var integer
     *
     * @Column (type="bigint")
     */
    protected $xcnPlan = 0;

    /**
     * Module name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $moduleName;

    /**
     * Author name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $authorName;

    /**
     * Description
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $description = '';

    /**
     * Icon URL
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $iconURL;

    /**
     * Icon URL
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $pageURL = '';

    /**
     * Icon URL
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $authorPageURL = '';

    /**
     * Module dependencies
     *
     * @var array
     *
     * @Column (type="array")
     */
    protected $dependencies = array();

    /**
     * Module tags
     *
     * @var array
     *
     * @Column (type="array")
     */
    protected $tags = array();

    /**
     * Flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $fromMarketplace = false;

    /**
     * Landing page flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $isLanding = false;

    /**
     * Position on the landing page
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $landingPosition = 0;

    /**
     * Public identifier (cache)
     *
     * @var string
     */
    protected $marketplaceID;

    /**
     * Flag (true if module is 'system module')
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $isSystem = false;

    /**
     * Flag (true if module has license text)
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $hasLicense = false;

    /**
     * Module availability in the store's edition
     * 0 - addon is not available in any editions
     * 1 - addon is available in store's edition
     * 2 - addon is available in other editions only
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $editionState = 0;

    /**
     * Available editions
     *
     * @var array
     *
     * @Column (type="array")
     */
    protected $editions = array();

    /**
     * XB product ID
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $xbProductId = 0;

    /**
     * Image URL cached
     *
     * @var string
     */
    protected $imageURLCached = array();

    /**
     * Flag (true if module is 'skin module')
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $isSkin = false;

    /**
     * Flag: true - module is private, 0 - public
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $private = false;

    /**
     * Returns string representation of module entity
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getActualName() . ' v' . $this->getVersion();
    }

    // {{{ Routines to access methods of (non)installed modules

    /**
     * Return main class name for current module
     *
     * @return string
     */
    public function getMainClass()
    {
        return '\XLite\Module\\' . $this->getActualName() . '\Main';
    }

    /**
     * Method to call functions from module main classes
     *
     * @param string $method Method to call
     * @param mixed  $result Method return value for the current class (model) OPTIONAL
     * @param array  $args   Call arguments OPTIONAL
     *
     * @return mixed
     */
    public function callModuleMethod($method, $result = null, array $args = array())
    {
        return $this->checkModuleMainClass()
            ? call_user_func_array(array($this->getMainClass(), $method), $args)
            : $result;
    }

    /**
     * Check if we can call method from the module main class
     *
     * @return boolean
     */
    public function checkModuleMainClass()
    {
        $result = $this->isInstalled();

        if ($result && !\Includes\Utils\Operator::checkIfClassExists($this->getMainClass())) {
            if (defined('LC_MODULE_CONTROL')) {
                $path = LC_DIR_CLASSES . \Includes\Utils\Converter::getClassFile($this->getMainClass());
                if (\Includes\Utils\FileManager::isFileReadable($path)) {
                    require_once $path;

                } else {
                    $result = false;
                }

            } else {
                $result = false;
            }
        }

        return $result;
    }

    // }}}

    // {{{ Some common getters and setters

    /**
     * Compose module actual name
     *
     * @return string
     */
    public function getActualName()
    {
        return \Includes\Utils\ModulesManager::getActualName($this->getAuthor(), $this->getName());
    }

    /**
     * Return module full version
     *
     * @return string
     */
    public function getVersion()
    {
        return \Includes\Utils\Converter::composeVersion($this->getMajorVersion(), $this->getFullMinorVersion());
    }

    /**
     * Return module full minor version
     *
     * @return string
     */
    public function getFullMinorVersion()
    {
        return $this->getMinorVersion()
            . ($this->getBuild() ? '.' . $this->getBuild() : '');
    }

    /**
     * Check if module has a custom icon
     *
     * @return boolean
     */
    public function hasIcon()
    {
        return (bool) $this->getPublicIconURL();
    }

    /**
     * Get public icon URL
     *
     * @return string
     */
    public function getPublicIconURL()
    {
        $image = 'icon.png';

        if (!isset($this->imageURLCached[$image])) {
            $this->imageURLCached[$image] = $this->getIconURL();

            if (!$this->imageURLCached[$image]) {
                $this->imageURLCached[$image] = $this->getPublicImageURL($image);
            }

            if ($this->imageURLCached[$image]) {
                $this->imageURLCached[$image] = preg_replace('/^https?:/', '', $this->imageURLCached[$image]);
            }
        }

        return $this->imageURLCached[$image];
    }

    /**
     * Get public image URL
     *
     * @param string $image Image name
     *
     * @return string
     */
    public function getPublicImageURL($image)
    {
        if (!isset($this->imageURLCached[$image])) {
            $path = \Includes\Utils\ModulesManager::getModuleImageFile($this->getAuthor(), $this->getName(), $image);

            if (\Includes\Utils\FileManager::isFileReadable($path)) {
                $this->imageURLCached[$image] = \XLite\Core\Converter::buildURL(
                    'module',
                    null,
                    array(
                        'author' => $this->getAuthor(),
                        'name'   => $this->getName(),
                        'image'  => $image
                    ),
                    'image.php'
                );
            } else {
                $this->imageURLCached[$image] = false;
            }
        }

        return $this->imageURLCached[$image];
    }

    /**
     * Return link to settings form
     *
     * @return string
     */
    public function getSettingsForm()
    {
        $params = array(
            'moduleId' => $this->getModuleId(),
            'returnTarget' => \XLite::getController()->getTarget(),
        );

        return $this->callModuleMethod('getSettingsForm')
            ?: \XLite\Core\Converter::buildURL('module', '', $params, \XLite::getAdminScript());
    }

    /**
     * Get list of dependency modules as Doctrine entities
     *
     * @param mixed $onlyDisabled Flag OPTIONAL
     *
     * @return array
     */
    public function getDependencyModules($onlyDisabled = false)
    {
        $result  = array();
        $classes = array_fill_keys($this->getDependencies(), true);

        if (!empty($classes)) {
            foreach ($this->getRepository()->getDependencyModules($classes) as $module) {
                unset($classes[$module->getActualName()]);

                if (!($onlyDisabled && $module->getEnabled())) {
                    $result[] = $module;
                }
            }

            foreach ($classes as $class => $tmp) {
                list($author, $name) = explode('\\', $class);

                $module = new \XLite\Model\Module();
                $module->setName($name);
                $module->setAuthor($author);
                $module->setModuleName($name);
                $module->setAuthorName($author);
                $module->setEnabled(false);
                $module->setInstalled(false);

                $result[] = $module;
            }
        }

        return $result;
    }

    /**
     * Get list of dependent modules as Doctrine entities
     *
     * @return array
     */
    public function getDependentModules()
    {
        $result  = array();
        $current = \Includes\Decorator\ADecorator::getModulesGraph()->find($this->getActualName());

        if ($current) {
            foreach ($current->getChildren() as $node) {
                $class = $node->getActualName();
                $result[$class] = $this->getRepository()
                    ->findOneBy(array_combine(array('author', 'name'), explode('\\', $class)), array('installed' => 'DESC'));
            }
        }

        return array_filter($result);
    }

    /**
     * Check if the module can be disabled
     *
     * @param boolean $safeCheck True - check if dependent modules can be disabled
     *
     * @return boolean
     */
    public function canDisable($safeCheck = false)
    {
        return !$this->isHardEnabled() && ($safeCheck || !$this->hasEnabledDependentModules());
    }

    /**
     * Return true if module enabled and cannot be disabled at all
     *
     * @return boolean
     */
    public function isHardEnabled()
    {
        $result = $this->getEnabled() && !$this->callModuleMethod('canDisable');

        if (!$result) {

            // Get list of installed modules which are depends on this module
            $dependent = $this->getDependentModules();

            if ($dependent) {
                foreach ($dependent as $dep) {
                    if ($dep->isHardEnabled()) {
                        $result = true;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Check if all dependent modules are enabled
     *
     * @return boolean
     */
    public function hasEnabledDependentModules()
    {
        $result = false;

        // Get list of installed modules which are depends on this module
        $dependent = $this->getDependentModules();

        if ($dependent) {
            foreach ($dependent as $dep) {
                if ($dep->getEnabled()) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Check - module can uninstall or not
     *
     * @return boolean
     */
    public function canUninstall()
    {
        return !$this->getEnabled() || !$this->callModuleMethod('isSeparateUninstall');
    }

    /**
     * Check if the module can be enabled
     *
     * @param boolean $safeCheck True - check if dependent modules can be enabled, false - dependent modules must be active
     *
     * @return boolean
     */
    public function canEnable($safeCheck = false)
    {
        $result = $this->isModuleCompatible();

        if ($result) {

            $dependencies = $this->getDependencies();

            if ($dependencies) {
                $modules = array_keys(\Includes\Utils\ModulesManager::getActiveModules());
                $result  = ! (bool) array_diff($dependencies, $modules);
            }

            if (!$result && $safeCheck) {
                $result = true;
                foreach ($this->getDependencyModules(true) as $dep) {
                    if ($dep->getFromMarketplace() || !$dep->canEnable($safeCheck)) {
                        $result = false;
                        break;
                    }
                }
            }
        }

        if ($result) {
            $result = !$this->hasWrongDependencies($safeCheck);
        }

        return $result;
    }

    /**
     * Return true if module compatible with current core.
     *
     * @return boolean
     */
    public function isModuleCompatible()
    {
        return \XLite::getInstance()->checkVersion($this->getMajorVersion(), '=')
            && \XLite::getInstance()->checkMinorVersion($this->getMinorRequiredCoreVersion(), '>=');
    }

    /**
     * Check if there are modules current module depends on
     *
     * @param boolean $safeCheck True - do not check module dependencies
     *
     * @return array
     */
    public function hasWrongDependencies($safeCheck = false)
    {
        return (!$safeCheck && (bool) $this->getDependencyModules(true)) || ((bool) $this->getEnabledMutualModules());
    }

    /**
     * Return list of modules current module requires to be disabled
     *
     * @return array
     */
    public function getEnabledMutualModules()
    {
        $list = array();

        $cacheKey = \Includes\Utils\ModulesManager::getActiveModulesHash() . $this->getActualName() . 'MutualModules';
        $cacheDriver = \XLite\Core\Database::getCacheDriver();

        if ($cacheDriver->contains($cacheKey)) {
            $cached = $cacheDriver->fetch($cacheKey);
            $list = is_array($cached)
                ? $cached
                : array();

        } else {
            $modules = \Includes\Utils\ModulesManager::getActiveModules();
            foreach ($modules as $m => $data) {
                $mutualModules = \Includes\Utils\ModulesManager::callModuleMethod($m, 'getMutualModulesList');

                if ($mutualModules && in_array($this->getActualName(), $mutualModules) && !isset($list[$m])) {
                    $list[$m] = \XLite\Core\Database::getRepo('XLite\Model\Module')
                        ->findOneBy(array_combine(array('author', 'name'), explode('\\', $m)));
                }
            }
            $cacheDriver->save($cacheKey, $list ?: 'EMPTY');
        }

        return $list;
    }

    /**
     * Check if the module is free
     *
     * @return boolean
     */
    public function isFree()
    {
        return 0 >= $this->getPrice();
    }

    /**
     * Check if module is already purchased
     *
     * @return boolean
     */
    public function isPurchased()
    {
        $xcnModulePlan = $this->getXcnPlan();

        return (bool) (static::NOT_XCN_MODULE == $xcnModulePlan
            ? $this->getLicenseKey()
            : $this->isAvailable() && $xcnModulePlan == \XLite::getXCNLicenseType()
        );
    }

    /**
     * Return true if module is not available for installation in XCN
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return static::NOT_AVAILABLE_MODULE != $this->getXcnPlan();
    }

    /**
     * Check for custom module
     *
     * @return boolean
     */
    public function isCustom()
    {
        $module = $this->getRepository()->getModuleFromMarketplace($this);

        return !isset($module)
            || (!$this->getFromMarketplace() && $module->getModuleID() === $this->getModuleID());
    }

    /**
     * Check for private module
     *
     * @return boolean
     */
    public function isPrivate()
    {
        $module = $this->getRepository()->getModuleFromMarketplace($this);

        return $module && $module->getPrivate();
    }

    /**
     * Check for private module
     *
     * @return boolean
     */
    public function isExtendedWarranty()
    {
        return false;
    }

    /**
     * Search for license key
     *
     * @return \XLite\Model\ModuleKey
     */
    public function getLicenseKey()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\ModuleKey')->findKey($this->getAuthor(), $this->getName());
    }

    /**
     * Return list of editions names
     *
     * @return array
     */
    public function getEditionNames()
    {
        $result = $this->getEditions();

        foreach ($result as $k => $v) {
            $result[$k] = preg_replace('/^(\d+)_(.+)$/', '\2', $v);
        }

        return $result;
    }

    /**
     * Get hash array of edition names IDs
     *
     * @return array
     */
    public function getEditionIds()
    {
        $result = $this->getEditions();

        foreach ($result as $k => $v) {
            preg_match('/^(\d+)_(.+)$/', $v, $match);
            $result[$match[2]] = $match[1];
        }

        return $result;
    }

    /**
     * Return currency for paid modules
     *
     * @return \XLite\Model\Currency
     */
    public function getCurrency()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Currency')->findOneByCode($this->currency)
            ?: $this->currency;
    }

    /**
     * Check if module is installed in LC
     *
     * @return boolean
     */
    public function isInstalled()
    {
        return $this->installed ?: (bool) $this->getRepository()->getModuleInstalled($this);
    }

    /**
     * Returns installed module
     *
     * @return \XLite\Model\Module
     */
    public function getModuleInstalled()
    {
        return $this->getRepository()->getModuleInstalled($this);
    }

    /**
     * Return some data to identify module
     *
     * @return array
     */
    public function getIdentityData()
    {
        $data = array(
            'author'       => $this->getAuthor(),
            'name'         => $this->getName(),
            'majorVersion' => $this->getMajorVersion(),
            'minorVersion' => $this->getMinorVersion(),
        );

        if ($this->getBuild()) {
            $data['build'] = $this->getBuild();
        }

        return $data;
    }

    /**
     * Generate marketplace ID
     *
     * @return string
     */
    public function getMarketplaceID()
    {
        if (!isset($this->marketplaceID)) {
            $this->marketplaceID = md5(implode(self::IDENTITY_SEPARATOR, $this->getIdentityData()));
        }

        return $this->marketplaceID;
    }

    /**
     * Return true if module is translation
     *
     * @return boolean
     */
    public function isTranslation()
    {
        $result = false;

        if (!$this->isInstalled()) {

            $tags = $this->getTags();

            if (is_array($tags) && in_array('Translation', $tags)) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Module has license restriction if the core has free license but the module does not have free edition
     * The empty editions field means the module has no license restriction
     *
     * @return boolean
     */
    public function hasLicenseRestriction()
    {
        $result = false;
        $coreKey = \XLite::getXCNLicenseKey();

        // The store without any license (trial license active) has no license-restricted module
        if ($coreKey !== '') {
            $editions = $this->getEditionNames();

            // The module with the empty editions structure has no restrictions
            if (!empty($editions)) {
                $freeEdition = \XLite\Core\Marketplace::getInstance()->getFreeLicenseEdition();

                // Module has restricted if there is no freeEdition in the editions structure
                $result = !in_array($freeEdition, $editions);
            }
        }

        return $result;
    }

    /**
     * Check if the module is defined as skin module
     *
     * @return boolean
     */
    public function isSkinModule()
    {
        // Check module type from classes/XLite/Modules/** for disabled modules
        if (!defined('LC_MODULE_CONTROL')) {
            define('LC_MODULE_CONTROL', true);
        }
        $type = $this->callModuleMethod('getModuleType');

        $result = (\XLite\Module\AModule::MODULE_TYPE_SKIN & (int)$type)
            === \XLite\Module\AModule::MODULE_TYPE_SKIN;

        return $result;
    }

    // }}}

    // {{{ Change module state routines

    /**
     * Lifecycle callback
     *
     * @return void
     *
     * @PreUpdate
     */
    public function prepareBeforeUpdate()
    {
        $changeSet = \XLite\Core\Database::getEM()->getUnitOfWork()->getEntityChangeSet($this);

        if (!empty($changeSet['enabled'])) {
            \XLite\Core\Database::getInstance()->setDisabledStructures(
                $this->getActualName(),
                $this->getEnabled()
                    ? array()
                    : \Includes\Utils\ModulesManager::getModuleProtectedStructures($this->getAuthor(), $this->getName())
            );

            $this->switchLinkedModels($this->getEnabled());
        }
    }

    /**
     * Switch linked models
     *
     * @param boolean $enabled Module enabled status
     *
     * @return void
     */
    protected function switchLinkedModels($enabled)
    {
        foreach ($this->getLinkedModelDefinition() as $repo) {
            \XLite\Core\Database::getRepo($repo)->switchModuleLink($enabled, $this);
        }
    }

    /**
     * Get linked model definition
     *
     * @return array
     */
    protected function getLinkedModelDefinition()
    {
        return array(
            'XLite\Model\Payment\Method',
        );
    }

    // }}}

    // {{{ List urls

    /**
     * Returns installed modules list url
     *
     * @return string
     */
    public function getInstalledURL()
    {
        $widget = new \XLite\View\ItemsList\Module\Manage();
        $limit = $widget->getPagerParamItemsPerPage();

        $pageId = $this->getRepository()->getInstalledPageId(
            $this->getAuthor(),
            $this->getName(),
            $limit
        );

        $params = array(
            'clearCnd'                              => 1,
            'clearSearch'                           => 1,
            \XLite\View\Pager\APager::PARAM_PAGE_ID => $pageId,
        );

        return \XLite::getInstance()->getShopURL(
            sprintf('%s#%s', \XLite\Core\Converter::buildURL('addons_list_installed', '', $params), $this->getName())
        );
    }

    /**
     * Returns marketplace modules list url
     *
     * @return string
     */
    public function getMarketplaceURL()
    {
        $moduleFormMarket = $this->getRepository()->getModuleFromMarketplace($this);

        $params = array();
        if ($moduleFormMarket) {
            $params = array(
                'moduleID'      => $moduleFormMarket->getModuleID(),
            );
        }

        return \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL('addons_list_marketplace', '', $params)
        );
    }

    // }}}

    /**
     * Get moduleID
     *
     * @return integer 
     */
    public function getModuleID()
    {
        return $this->moduleID;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Module
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return Module
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set authorEmail
     *
     * @param string $authorEmail
     * @return Module
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
        return $this;
    }

    /**
     * Get authorEmail
     *
     * @return string 
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Module
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
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
     * Set installed
     *
     * @param boolean $installed
     * @return Module
     */
    public function setInstalled($installed)
    {
        $this->installed = $installed;
        return $this;
    }

    /**
     * Get installed
     *
     * @return boolean 
     */
    public function getInstalled()
    {
        return $this->installed;
    }

    /**
     * Set yamlLoaded
     *
     * @param boolean $yamlLoaded
     * @return Module
     */
    public function setYamlLoaded($yamlLoaded)
    {
        $this->yamlLoaded = $yamlLoaded;
        return $this;
    }

    /**
     * Get yamlLoaded
     *
     * @return boolean 
     */
    public function getYamlLoaded()
    {
        return $this->yamlLoaded;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Module
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set rating
     *
     * @param decimal $rating
     * @return Module
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * Get rating
     *
     * @return decimal 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set votes
     *
     * @param integer $votes
     * @return Module
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
        return $this;
    }

    /**
     * Get votes
     *
     * @return integer 
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Set downloads
     *
     * @param integer $downloads
     * @return Module
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;
        return $this;
    }

    /**
     * Get downloads
     *
     * @return integer 
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * Set price
     *
     * @param decimal $price
     * @return Module
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return decimal 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return Module
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Set majorVersion
     *
     * @param string $majorVersion
     * @return Module
     */
    public function setMajorVersion($majorVersion)
    {
        $this->majorVersion = $majorVersion;
        return $this;
    }

    /**
     * Get majorVersion
     *
     * @return string 
     */
    public function getMajorVersion()
    {
        return $this->majorVersion;
    }

    /**
     * Set minorVersion
     *
     * @param integer $minorVersion
     * @return Module
     */
    public function setMinorVersion($minorVersion)
    {
        $this->minorVersion = $minorVersion;
        return $this;
    }

    /**
     * Get minorVersion
     *
     * @return integer 
     */
    public function getMinorVersion()
    {
        return $this->minorVersion;
    }

    /**
     * Set build
     *
     * @param integer $minorVersion
     * @return Module
     */
    public function setBuild($build)
    {
        $this->build = $build;
        return $this;
    }

    /**
     * Get build
     *
     * @return integer
     */
    public function getBuild()
    {
        return $this->build;
    }

    /**
     * Set minorRequiredCoreVersion
     *
     * @param integer $minorRequiredCoreVersion
     * @return Module
     */
    public function setMinorRequiredCoreVersion($minorRequiredCoreVersion)
    {
        $this->minorRequiredCoreVersion = $minorRequiredCoreVersion;
        return $this;
    }

    /**
     * Get minorRequiredCoreVersion
     *
     * @return integer 
     */
    public function getMinorRequiredCoreVersion()
    {
        return $this->minorRequiredCoreVersion;
    }

    /**
     * Set revisionDate
     *
     * @param integer $revisionDate
     * @return Module
     */
    public function setRevisionDate($revisionDate)
    {
        $this->revisionDate = $revisionDate;
        return $this;
    }

    /**
     * Get revisionDate
     *
     * @return integer 
     */
    public function getRevisionDate()
    {
        return $this->revisionDate;
    }

    /**
     * Set packSize
     *
     * @param bigint $packSize
     * @return Module
     */
    public function setPackSize($packSize)
    {
        $this->packSize = $packSize;
        return $this;
    }

    /**
     * Get packSize
     *
     * @return bigint 
     */
    public function getPackSize()
    {
        return $this->packSize;
    }

    /**
     * Set xcnPlan
     *
     * @param bigint $xcnPlan
     * @return Module
     */
    public function setXcnPlan($xcnPlan)
    {
        $this->xcnPlan = $xcnPlan;
        return $this;
    }

    /**
     * Get xcnPlan
     *
     * @return bigint 
     */
    public function getXcnPlan()
    {
        return $this->xcnPlan;
    }

    /**
     * Set moduleName
     *
     * @param string $moduleName
     * @return Module
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
     * Set authorName
     *
     * @param string $authorName
     * @return Module
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
        return $this;
    }

    /**
     * Get authorName
     *
     * @return string 
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * Set description
     *
     * @param text $description
     * @return Module
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set iconURL
     *
     * @param string $iconURL
     * @return Module
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
     * Set pageURL
     *
     * @param string $pageURL
     * @return Module
     */
    public function setPageURL($pageURL)
    {
        $this->pageURL = $pageURL;
        return $this;
    }

    /**
     * Get pageURL
     *
     * @return string 
     */
    public function getPageURL()
    {
        return $this->pageURL;
    }

    /**
     * Set authorPageURL
     *
     * @param string $authorPageURL
     * @return Module
     */
    public function setAuthorPageURL($authorPageURL)
    {
        $this->authorPageURL = $authorPageURL;
        return $this;
    }

    /**
     * Get authorPageURL
     *
     * @return string 
     */
    public function getAuthorPageURL()
    {
        return $this->authorPageURL;
    }

    /**
     * Set dependencies
     *
     * @param array $dependencies
     * @return Module
     */
    public function setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;
        return $this;
    }

    /**
     * Get dependencies
     *
     * @return array 
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Set tags
     *
     * @param array $tags
     * @return Module
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Get tags
     *
     * @return array 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set fromMarketplace
     *
     * @param boolean $fromMarketplace
     * @return Module
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
     * Set isLanding
     *
     * @param boolean $isLanding
     * @return Module
     */
    public function setIsLanding($isLanding)
    {
        $this->isLanding = $isLanding;
        return $this;
    }

    /**
     * Get isLanding
     *
     * @return boolean 
     */
    public function getIsLanding()
    {
        return $this->isLanding;
    }

    /**
     * Set landingPosition
     *
     * @param integer $landingPosition
     * @return Module
     */
    public function setLandingPosition($landingPosition)
    {
        $this->landingPosition = $landingPosition;
        return $this;
    }

    /**
     * Get landingPosition
     *
     * @return integer 
     */
    public function getLandingPosition()
    {
        return $this->landingPosition;
    }

    /**
     * Set isSystem
     *
     * @param boolean $isSystem
     * @return Module
     */
    public function setIsSystem($isSystem)
    {
        $this->isSystem = $isSystem;
        return $this;
    }

    /**
     * Get isSystem
     *
     * @return boolean 
     */
    public function getIsSystem()
    {
        return $this->isSystem;
    }

    /**
     * Set hasLicense
     *
     * @param boolean $hasLicense
     * @return Module
     */
    public function setHasLicense($hasLicense)
    {
        $this->hasLicense = $hasLicense;
        return $this;
    }

    /**
     * Get hasLicense
     *
     * @return boolean 
     */
    public function getHasLicense()
    {
        return $this->hasLicense;
    }

    /**
     * Set editionState
     *
     * @param integer $editionState
     * @return Module
     */
    public function setEditionState($editionState)
    {
        $this->editionState = $editionState;
        return $this;
    }

    /**
     * Get editionState
     *
     * @return integer 
     */
    public function getEditionState()
    {
        return $this->editionState;
    }

    /**
     * Set editions
     *
     * @param array $editions
     * @return Module
     */
    public function setEditions($editions)
    {
        $this->editions = $editions;
        return $this;
    }

    /**
     * Get editions
     *
     * @return array 
     */
    public function getEditions()
    {
        return $this->editions;
    }

    /**
     * Set xbProductId
     *
     * @param integer $xbProductId
     * @return Module
     */
    public function setXbProductId($xbProductId)
    {
        $this->xbProductId = $xbProductId;
        return $this;
    }

    /**
     * Get xbProductId
     *
     * @return integer 
     */
    public function getXbProductId()
    {
        return $this->xbProductId;
    }

    /**
     * Set isSkin
     *
     * @param boolean $isSkin
     * @return Module
     */
    public function setIsSkin($isSkin)
    {
        $this->isSkin = $isSkin;
        return $this;
    }

    /**
     * Get isSkin
     *
     * @return boolean 
     */
    public function getIsSkin()
    {
        return $this->isSkin;
    }

    /**
     * Set private
     *
     * @param boolean $value
     * @return Module
     */
    public function setPrivate($value)
    {
        $this->private = $value;
        return $this;
    }

    /**
     * Get private
     *
     * @return boolean
     */
    public function getPrivate()
    {
        return $this->private;
    }
}
