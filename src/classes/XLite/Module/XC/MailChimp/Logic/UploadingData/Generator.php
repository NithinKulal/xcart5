<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\UploadingData;

use XLite\Module\XC\MailChimp\Core\MailChimp;
use XLite\Module\XC\MailChimp\Core\MailChimpECommerce;

/**
 * Generator
 */
class Generator extends \XLite\Logic\AGenerator
{
    /**
     * Steps (cache)
     *
     * @var array
     */
    protected $steps;

    /**
     * Current step index
     *
     * @var integer
     */
    protected $currentStep;

    /**
     * Count (cached)
     *
     * @var integer
     */
    protected $countCache;

    /**
     * Flag: is export in progress (true) or no (false)
     *
     * @var boolean
     */
    protected static $inProgress = false;

    /**
     * Set inProgress flag value
     *
     * @param boolean $value Value
     *
     * @return void
     */
    public function setInProgress($value)
    {
        static::$inProgress = $value;
    }

    /**
     * @param $options \ArrayObject
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    // {{{ Steps

    /**
     * @return array
     */
    protected function getStepsList()
    {
        return array(
            'XLite\Module\XC\MailChimp\Logic\UploadingData\Step\RemoveProducts',
            'XLite\Module\XC\MailChimp\Logic\UploadingData\Step\RemoveOrders',
            'XLite\Module\XC\MailChimp\Logic\UploadingData\Step\Products',
            'XLite\Module\XC\MailChimp\Logic\UploadingData\Step\Orders',
        );
    }

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        $steps = array();
        $options = $this->getOptions();
        if (isset($options['steps'])) {
            $requestedSteps = array_map(function($stepName) {
                return strtolower($stepName);
            }, $options['steps']);

            if (is_array($requestedSteps)) {
                foreach ($this->getStepsList() as $step) {
                    $_step = explode('\\', $step);
                    $_step = array_pop($_step);
                    $_step = strtolower($_step);

                    if (in_array($_step, $requestedSteps)) {
                        $steps[] = $step;
                    }
                }
            }
        }

        return $steps;
    }

    /**
     * @inheritDoc
     */
    protected function initialize()
    {
        parent::initialize();

        $options = $this->getOptions();

        $lists = isset($options['lists'])
            ? $options['lists']
            : null;

        foreach ($lists as $listId => $value) {
            $storeName = MailChimp::getInstance()->getStoreName();
            $storeId = MailChimp::getInstance()->getStoreId($listId);
            if ($storeId) {
                $ecCore = MailChimpECommerce::getInstance();
                if (!$ecCore->getStore($storeId)) {
                    $ecCore->createStore(
                        [
                            'campaign_id'   => '',
                            'store_id'      => $storeId,
                            'store_name'    => $storeName,
                            'currency_code' => \XLite::getInstance()->getCurrency()->getCode(),
                            'is_main'       => $value
                        ],
                        $listId
                    );
                } else {
                    $existingStore = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\Store')->find($storeId);

                    if ($existingStore) {
                        $existingStore->setMain($value);
                    } else {
                        $ecCore->createStoreReference(
                            $listId,
                            $storeId,
                            $storeName,
                            $value
                        );
                    }
                }
            }
            
            if (!isset($options['stores'])) {
                $options['stores'] = [];
            }
            
            $options['stores'][] = $storeId;
        }
        
        $this->setOptions($options);
    }

    // }}}

    // {{{ SeekableIterator, Countable

    /**
     * \Counable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            if (!isset($this->options['count'])) {
                $this->options['count'] = 0;
                foreach ($this->getSteps() as $step) {
                    $this->options['count'] += $step->count();
                    $this->options['count' . get_class($step)] = $step->count();
                }
            }
            $this->countCache = $this->options['count'];
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Service variable names

    /**
     * Get resizeTickDuration TmpVar name
     *
     * @return string
     */
    public static function getTickDurationVarName()
    {
        return static::getEventName() . 'TickDuration';
    }

    /**
     * Get resize cancel flag name
     *
     * @return string
     */
    public static function getCancelFlagVarName()
    {
        return static::getEventName() . 'CancelFlag';
    }

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'MailChimpUploadingData';
    }

    /**
     * Get export lock key
     *
     * @return string
     */
    public static function getLockKey()
    {
        return static::getEventName();
    }

    // }}}
}