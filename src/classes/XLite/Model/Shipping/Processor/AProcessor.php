<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Shipping\Processor;

/**
 * Shipping processor model
 */
abstract class AProcessor extends \XLite\Base\SuperClass
{
    const STATE_ALL = 'all';
    const STATE_ENABLED_ONLY = 'enabled_only';
    const MINIMUM_ITEM_WEIGHT = 0.01;

    /**
     * Processor's shipping methods (runtime cache)
     *
     * @var array
     */
    protected $methods;

    /**
     * Module processor cache object
     * false                        - it is not initialized yet
     * null                         - no payment processor
     * \XLite\Model\Module class    - shipping processor assigned
     *
     * @var boolean|null|\XLite\Model\Module
     */
    protected $moduleCache = false;

    /**
     * Log of request/response pairs during communication with a shipping server
     *
     * @var array
     */
    protected $apiCommunicationLog;

    /**
     * Error message
     * @todo: rename to 'error'
     *
     * @var string
     */
    protected $errorMsg;

    /**
     * Returns processor Id
     *
     * @return string
     */
    abstract public function getProcessorId();

    /**
     * Returns processor name
     *
     * @return string
     */
    abstract public function getProcessorName();

    /**
     * Define public constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns settings template
     *
     * @return string
     */
    public function getSettingsTemplate()
    {
        $module = $this->getModule();

        return $module
            ? sprintf('modules/%s/%s/settings.twig', $module->getAuthor(), $module->getName())
            : '';
    }

    /**
     * Returns test template
     *
     * @return string
     */
    public function getTestTemplate()
    {
        $module = $this->getModule();

        return $module
            ? sprintf('modules/%s/%s/test.twig', $module->getAuthor(), $module->getName())
            : '';
    }

    /**
     * Returns url for sign up
     *
     * @return string
     */
    public function getSignUpURL()
    {
        return '';
    }

    /**
     * Returns url for sign up
     *
     * @return string
     */
    public function getSettingsURL()
    {
        return '';
    }

    /**
     * Check test mode
     *
     * @return boolean
     */
    public function isTestMode()
    {
        return false;
    }

    /**
     * Returns activity status
     *
     * @return boolean
     */
    public function isEnabled()
    {
        /** @var \XLite\Model\Repo\Shipping\Method $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');
        $onlineMethod = $repo->findOnlineCarrier($this->getProcessorId());

        return $onlineMethod ? $onlineMethod->getEnabled() : false;
    }

    /**
     * Get processor module
     *
     * @return \XLite\Model\Module|null
     */
    public function getModule()
    {
        if (false === $this->moduleCache) {
            $this->moduleCache = preg_match('/XLite\\\Module\\\(\w+)\\\(\w+)\\\/S', get_called_class(), $match)
                ? \XLite\Core\Database::getRepo('XLite\Model\Module')->findModuleByName($match[1] . '\\' . $match[2])
                : null;
        }

        return $this->moduleCache;
    }

    /**
     * Get shipping method admin zone icon URL
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Shipping\Method $method)
    {
        return null;
    }

    /**
     * Get list of address fields required by shipping processor to calculate rates
     *
     * @return array
     */
    public function getRequiredAddressFields()
    {
        return array();
    }

    /**
     * Returns true if shipping methods names may be modified by admin
     *
     * @return boolean
     */
    public function isMethodNamesAdjustable()
    {
        return true;
    }

    /**
     * Returns true if shipping methods can be removed by admin
     *
     * @return boolean
     */
    public function isMethodDeleteEnabled()
    {
        return false;
    }

    // {{{ Rates

    /**
     * Returns processor's shipping methods rates
     *
     * @param array|\XLite\Logic\Order\Modifier\Shipping $inputData   Shipping order modifier or array of data
     * @param boolean                                    $ignoreCache Flag: if true then do not get rates
     *                                                                from cache OPTIONAL
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    public function getRates($inputData, $ignoreCache = false)
    {
        $this->setError();
        $rates = array();

        if (!$this->isConfigured()) {
            $this->setError(sprintf('%s module is not configured', $this->getProcessorName()));

        } elseif ($this->hasMethods(static::STATE_ENABLED_ONLY)) {
            $data = $this->prepareInputData($inputData);
            if ($data) {
                $rates = $this->performRequest($data, $ignoreCache);

                if ($rates) {
                    $this->postProcessRates($rates, $data, $ignoreCache);
                }
            } else {
                $this->setError('Wrong input data');
            }
        }

        return $rates;
    }

    /**
     * Prepare input data
     *
     * @param array|\XLite\Logic\Order\Modifier\Shipping $inputData Shipping order modifier (from order) or
     *                                                              array of input data (from test controller)
     *
     * @return array
     */
    protected function prepareInputData($inputData)
    {
        $result = $inputData instanceof \XLite\Logic\Order\Modifier\Shipping
            ? $this->prepareDataFromModifier($inputData)
            : $this->prepareDataFromArray($inputData);

        return $this->postProcessInputData($result);
    }

    /**
     * Prepare input data from order modifier
     *
     * @param \XLite\Logic\Order\Modifier\Shipping $inputData Shipping order modifier
     *
     * @return array
     */
    protected function prepareDataFromModifier(\XLite\Logic\Order\Modifier\Shipping $inputData)
    {
        return array();
    }

    /**
     * Prepare input data from array
     *
     * @param array $inputData Array of input data (from test controller)
     *
     * @return array
     */
    protected function prepareDataFromArray(array $inputData)
    {
        return $inputData;
    }

    /**
     * Post process input data
     *
     * @param array $inputData Prepared input data
     *
     * @return array
     */
    protected function postProcessInputData(array $inputData)
    {
        return $inputData;
    }

    /**
     * Performs request to carrier server and returns array of rates
     *
     * @param array   $data        Array of request parameters
     * @param boolean $ignoreCache Flag: if true then do not get rates from cache
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    protected function performRequest($data, $ignoreCache)
    {
        return array();
    }

    /**
     * Post process rates
     *
     * @param \XLite\Model\Shipping\Rate[] $rates       Rates
     * @param array                        $data        Prepared input data
     * @param boolean                      $ignoreCache Flag: if true then do not get rates
     *                                                    from cache OPTIONAL
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    protected function postProcessRates($rates, $data, $ignoreCache)
    {
        return $rates;
    }

    /**
     * Returns current processor shipping methods
     *
     * @param string $state Method state flag
     *
     * @return \XLite\Model\Shipping\Method[]
     */
    protected function getMethods($state = self::STATE_ENABLED_ONLY)
    {
        $methods = $this->fetchMethods();

        return static::STATE_ENABLED_ONLY === $state
            ? array_filter($methods, function ($item) {
                /** @var \XLite\Model\Shipping\Method $item */
                return $item->isEnabled();
            })
            : $methods;
    }

    /**
     * Returns true if current processor has shipping methods
     *
     * @param string $state Method state flag
     *
     * @return boolean
     */
    protected function hasMethods($state = self::STATE_ENABLED_ONLY)
    {
        return (bool) $this->getMethods($state);
    }

    /**
     * Fetch methods from database
     *
     * @return \XLite\Model\Shipping\Method[]
     */
    protected function fetchMethods()
    {
        if (null === $this->methods) {
            /** @var \XLite\Model\Repo\Shipping\Method $repo */
            $repo = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');
            $this->methods = $repo->findMethodsByProcessor($this->getProcessorId(), false);
        }

        return $this->methods ?: array();
    }

    /**
     * Returns method by code
     *
     * @param string $code  Method code
     * @param string $state Method state flag
     *
     * @return \XLite\Model\Shipping\Method|null
     */
    protected function getMethodByCode($code, $state = self::STATE_ENABLED_ONLY)
    {
        $methods = $this->getMethods($state);

        return is_array($methods)
            ? array_reduce($methods, function ($carry, $item) use ($code) {
                /** @var \XLite\Model\Shipping\Method $item */
                return $carry ?: ($code === $item->getCode() ? $item : null);
            }, null)
            : null;
    }

    /**
     * Create method
     *
     * @param string  $code    Method code
     * @param string  $name    Method name
     * @param boolean $enabled Enabled state OPTIONAL
     *
     * @return \XLite\Model\Shipping\Method
     */
    protected function createMethod($code, $name, $enabled = true)
    {
        $method = $this->getMethodByCode($code);
        if (null === $method) {
            /** @var \XLite\Model\Repo\Shipping\Method $repo */
            $repo = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');

            $method = $repo->createShippingMethod(
                $this->prepareCreateMethodData($code, $name, $enabled)
            );
            $this->methods[] = $method;
        }

        return $method;
    }

    /**
     * Prepare data for methods creation
     *
     * @param string  $code    Method code
     * @param string  $name    Method name
     * @param boolean $enabled Enabled state OPTIONAL
     *
     * @return array
     */
    protected function prepareCreateMethodData($code, $name, $enabled = true)
    {
        return array(
            'processor' => $this->getProcessorId(),
            'carrier'   => $this->getProcessorId(),
            'code'      => $code,
            'enabled'   => (bool) $enabled,
            'position'  => 0,
            'name'      => $name,
        );
    }

    // }}}

    // {{{ Configuration

    /**
     * Check - processor is configured or not
     *
     * @return boolean
     */
    public function isConfigured()
    {
        return true;
    }

    /**
     * Returns current configuration
     *
     * @return \XLite\Core\ConfigCell
     */
    protected function getConfiguration()
    {
        $result = null;
        $path = $this->getConfigurationPath();

        if ($path) {
            $result = $this->getCommonConfiguration();
            while ($node = array_shift($path)) {
                $result = $result->{$node};
            }
        }

        return $result;
    }

    /**
     * Returns common configuration
     *
     * @return \XLite\Core\ConfigCell
     */
    protected function getCommonConfiguration()
    {
        return \XLite\Core\Config::getInstance();
    }

    /**
     * Returns configuration category
     *
     * @return string
     */
    protected function getConfigurationPath()
    {
        return preg_match('/XLite\\\Module\\\(\w+)\\\(\w+)\\\/S', get_called_class(), $match)
            ? array($match[1] ,$match[2])
            : null;
    }

    // }}}

    // {{{ Package

    /**
     * Get packages for shipment
     *
     * @param \XLite\Logic\Order\Modifier\Shipping $modifier Shipping modifier
     *
     * @return array
     */
    public function getPackages($modifier)
    {
        \XLite\Core\Package::getInstance()->setMinimumItemWeight($this->getMinimumItemWeight());

        return \XLite\Core\Package::getInstance()->getPackages($modifier->getItems(), $this->getPackageLimits());
    }

    /**
     * Get default package limits
     *
     * @return array
     */
    protected function getPackageLimits()
    {
        return array();
    }

    /**
     * Returns minimum it item weight
     *
     * @return float
     */
    protected function getMinimumItemWeight()
    {
        return static::MINIMUM_ITEM_WEIGHT;
    }

    // }}}

    // {{{ Tracking information

    /**
     * Defines whether the form must be used for tracking information.
     * The 'getTrackingInformationURL' result will be used as tracking link instead
     *
     * @param string $trackingNumber Tracking number value
     *
     * @return boolean
     */
    public function isTrackingInformationForm($trackingNumber)
    {
        return true;
    }

    /**
     * This method must return the URL to the detailed tracking information about the package.
     * Tracking number is provided.
     *
     * @param string $trackingNumber
     *
     * @return null|string
     */
    public function getTrackingInformationURL($trackingNumber)
    {
        return null;
    }

    /**
     * This method must return the form method 'post' or 'get' value.
     *
     * @param string $trackingNumber
     *
     * @return string
     */
    public function getTrackingInformationMethod($trackingNumber)
    {
        return 'get';
    }

    /**
     * Defines the form parameters of tracking information form
     *
     * @param string $trackingNumber Tracking number
     *
     * @return array Array of form parameters
     */
    public function getTrackingInformationParams($trackingNumber)
    {
        return array();
    }

    // }}}

    // {{{ Cache

    /**
     * Get key hash
     * todo: allow array as key
     *
     * @param string $key Key
     *
     * @return string
     */
    protected function getKeyHash($key)
    {
        return md5($key);
    }

    /**
     * getDataFromCache
     *
     * @param string $key Key of a cache cell
     *
     * @return mixed
     */
    protected function getDataFromCache($key)
    {
        $data = null;
        $cacheDriver = \XLite\Core\Database::getCacheDriver();
        $key = $this->getKeyHash($key);

        if ($cacheDriver->contains($key)) {
            $data = $cacheDriver->fetch($key);
        }

        return $data;
    }

    /**
     * saveDataInCache
     *
     * @param string  $key      Key of a cache cell
     * @param mixed   $data     Data object for saving in the cache
     * @param integer $lifeTime The cache lifetime.
     *
     * @return void
     */
    protected function saveDataInCache($key, $data, $lifeTime = 0)
    {
        \XLite\Core\Database::getCacheDriver()->save($this->getKeyHash($key), $data, $lifeTime);
    }

    // }}}

    // {{{ Logging

    /**
     * Returns an API communication log
     * @deprecated use #getApiCommunicationMessage instead
     *
     * @return array
     */
    public function getApiCommunicationLog()
    {
        return $this->getApiCommunicationMessage();
    }

    /**
     * Returns an API communication message
     *
     * @return array
     */
    public function getApiCommunicationMessage()
    {
        return $this->apiCommunicationLog;
    }

    /**
     * Check for error
     *
     * @return boolean
     */
    public function hasError()
    {
        return (bool) $this->errorMsg;
    }

    /**
     * Returns error message
     *
     * @return string
     */
    public function getError()
    {
        return $this->errorMsg;
    }

    /**
     * Returns error message
     * @deprecated Use #getError() instead
     *
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->getError();
    }

    /**
     * Write transaction log
     * @deprecated
     *
     * @return void
     */
    public function logTransaction()
    {
        $this->flushErrorLog();
    }

    /**
     * Write error log
     *
     * @return void
     */
    public function flushErrorLog()
    {
        if ($this->hasError()) {
            \XLite\Logger::getInstance()->log($this->formatError($this->getError()));
        }
    }

    /**
     * Add message to custom log
     *
     * @param mixed $message Message to log
     *
     * @return void
     */
    protected function log($message)
    {
        $message = is_scalar($message) ? (string) $message : var_export($message, true);

        \XLite\Logger::logCustom($this->getProcessorId(), $message);
    }

    /**
     * Add api communication message
     *
     * @param string $message API communication log message
     *
     * @return void
     */
    protected function addApiCommunicationMessage($message)
    {
        $this->apiCommunicationLog[] = $message;
    }

    /**
     * Set error message
     *
     * @param string|null $error Error message OPTIONAL
     *
     * @return void
     */
    protected function setError($error = null)
    {
        $this->errorMsg = $error;
    }

    /**
     * Format error message
     *
     * @param string $error Message to format
     *
     * @return string
     */
    protected function formatError($error)
    {
        return sprintf('[%s] Error: %s', $this->getProcessorName(), $error);
    }

    // }}}
}
