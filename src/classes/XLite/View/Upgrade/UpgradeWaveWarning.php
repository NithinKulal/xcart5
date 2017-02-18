<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade;

/**
 * Class UpgradeWaveWarning
 *
 * @ListChild (list="admin.center", zone="admin", weight="0")
 */
class UpgradeWaveWarning extends \XLite\View\AView
{
    protected static $waves;

    /**
     * Return list of allowed targets
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'upgrade';

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return \XLite\Upgrade\Cell::getInstance()->getEntries()
            && !\XLite\Upgrade\Cell::getInstance()->isUpgraded()
            && !$this->isUpgradeWaveValid();
    }

    /**
     * There is no way to check if wave in merchant
     * So we are checking index here
     *
     * @return boolean
     */
    protected function isUpgradeWaveValid()
    {
        $value = $this->getWave();

        // Wave is not set (sees merchant waves only), or set to merchant wave explicitly
        return !$value || $value === $this->getMerchantWaveIndex();
    }

    /**
     * @return int
     */
    protected function getMerchantWaveIndex()
    {
        return 127;
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    public function getDir()
    {
        return 'upgrade/wave_warning';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * @return string
     */
    public function getWarningText()
    {
        $waveName = $this->getWaveName();

        return static::t(
            'upgrade_warning_text',
            [
                'wave_name'     => $waveName,
                'link_to_waves' => static::buildURL('settings','', [ 'page' => 'Environment'])
            ]
        );
    }
    /**
     * Get wave name
     *
     * @return string
     */
    protected function getWaveName()
    {
        $result = 'developer';

        $value = $this->getWave();
        $options = $this->getWaves();

        if (isset($options[$value])) {
            $result = $options[$value];
        }

        return $result;
    }

    /**
     * @return int
     */
    protected function getWave()
    {
        return intval(\XLite\Core\Config::getInstance()->Environment->upgrade_wave);
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
}
