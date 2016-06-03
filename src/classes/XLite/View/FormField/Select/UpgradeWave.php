<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Upgrade wave selector
 */
class UpgradeWave extends \XLite\View\FormField\Select\Regular
{
    /**
     * Waves list
     *
     * @var array
     */
    protected static $waves = null;

    /**
     * Return CSS files list
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'form_field/select_upgrade_wave.css';

        return $list;
    }

    /**
     * Get current wave value
     *
     * @return string
     */
    public function getValue()
    {
        $value = parent::getValue();

        $waves = $this->getWaves();

        if ($waves) {
            if (empty($value) || (!isset($waves[$value]) && !is_numeric($value))) {
                $waveKeys = array_keys($waves);
                $value = array_pop($waveKeys);
            }
        }

        return $value;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = $this->getWaves();
        $value = $this->getValue();

        if (!isset($options[$value])) {
            $options = array_merge(
                array($value => static::t('Tester')),
                $options
            );
        }

        return $options;
    }

    /**
     * Get list of upgrade waves
     *
     * @return array
     */
    protected function getWaves()
    {
        if (!isset(static::$waves)) {
            static::$waves = \XLite\Core\Marketplace::getInstance()->getWaves();
        }

        return static::$waves;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return $this->getWaves() && !$this->isDefaultFreeLicenseActivated()
            ? 'select_upgrade_wave.twig'
            : 'label.twig';
    }

    /**
     * Return true if default free license key is activated
     *
     * @return boolean
     */
    protected function isDefaultFreeLicenseActivated()
    {
        return \XLite\Core\Marketplace::XC_FREE_LICENSE_KEY == \XLite::getXCNLicenseKey();
    }

    /**
     * Get label value
     *
     * @return string
     */
    protected function getLabelValue()
    {
        return $this->isDefaultFreeLicenseActivated()
            ? static::t('Upgrade access level cannot be changed for default free license')
            : static::t('There are no activated license keys');
    }

    /**
     * Get help message for tooltip
     *
     * @return string
     */
    protected function getHelpMessage()
    {
        return static::t('Upgrade access level tooltip message');
    }
}
