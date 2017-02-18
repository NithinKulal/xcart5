<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\Menu\Admin\LeftMenu\Info;

use XLite\Core\Database;
use XLite\Core\TmpVars;
use XLite\Model\Module;
use XLite\Module\QSL\CloudSearch\Core\ServiceApiClient;
use XLite\Module\QSL\CloudSearch\Main;
use XLite\View\Menu\Admin\LeftMenu\ANodeNotification;

/**
 *
 */
class TrialNotice extends ANodeNotification
{
    const MIN_TRIAL_DAYS_LEFT_WARNING = 7;

    const CACHE_TTL_HOURS = 4;

    protected $trialInfo;

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return [
            'modules/QSL/CloudSearch/trial_notice.less',
        ];
    }

    /**
     * Check if data is updated (must be fast)
     *
     * @return boolean
     */
    public function isUpdated()
    {
        return $this->getLastReadTimestamp() < $this->getLastUpdateTimestamp();
    }

    /**
     * Get cache TTL (seconds)
     *
     * @return integer
     */
    public function getCacheTTL()
    {
        return self::CACHE_TTL_HOURS * 3600;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
               && Main::isConfigured()
               && $this->getExpirationMessage() !== null;
    }

    /**
     * Return update timestamp
     *
     * @return integer
     */
    protected function getLastUpdateTimestamp()
    {
        $tmpVars = TmpVars::getInstance();

        return $tmpVars->cloudSearchTrialInfoUpdatedAt;
    }

    /**
     * Returns node style class
     *
     * @return array
     */
    protected function getNodeStyleClasses()
    {
        $list = parent::getNodeStyleClasses();

        $list[] = 'cloud-search-notice';

        return $list;
    }

    /**
     * Returns icon
     *
     * @return string
     */
    protected function getIcon()
    {
        return $this->getSVGImage('images/info.svg');
    }

    /**
     * Returns header url
     *
     * @return string
     */
    protected function getHeaderUrl()
    {
        /** @var Module $module */
        $module = Database::getRepo('XLite\Model\Module')->findModuleByName('QSL\CloudSearch');

        return $this->buildURL('module', '', ['moduleId' => $module->getModuleID()]);
    }

    /**
     * Returns header
     *
     * @return string
     */
    protected function getHeader()
    {
        return $this->getExpirationMessage();
    }

    /**
     * Get trial expired or expires soon message
     *
     * @return null|string
     */
    protected function getExpirationMessage()
    {
        if ($this->isTrial()) {
            if ($this->isTrialExpired()) {
                return self::t('CloudSearch trial period expired');
            } else if ($this->doesTrialExpireSoon()) {
                return self::t('CloudSearch trial period expires soon');
            }
        }

        return null;
    }

    /**
     * Check if
     *
     * @return bool
     */
    protected function isTrial()
    {
        $trialInfo = $this->getTrialInfoCached();

        return $trialInfo !== null;
    }

    /**
     * Check if trial is expired
     *
     * @return mixed
     */
    protected function isTrialExpired()
    {
        $trialInfo = $this->getTrialInfoCached();

        return $trialInfo['expired'];
    }

    /**
     * Get number of days left in trial
     *
     * @return int
     */
    protected function doesTrialExpireSoon()
    {
        $trialInfo = $this->getTrialInfoCached();

        return $trialInfo['expiresSoon'];
    }

    /**
     * Get CloudSearch trial info (cached)
     *
     * @return mixed|null
     */
    protected function getTrialInfoCached()
    {
        if (!isset($this->trialInfo)) {
            $cacheParams   = $this->getCacheParameters();
            $cacheParams[] = 'getTrialInfo';

            $this->trialInfo = $this->getCache()->get($cacheParams);

            if ($this->trialInfo === null) {
                $this->trialInfo = $this->getTrialInfo();

                $this->getCache()->set($cacheParams, $this->trialInfo, $this->getCacheTTL());
            }
        }

        return $this->trialInfo;
    }

    /**
     * Get CloudSearch trial info
     *
     * @return mixed|null
     */
    protected function getTrialInfo()
    {
        $client = new ServiceApiClient();

        $tmpVars = TmpVars::getInstance();

        $trialInfo = $this->getTrialInfoFromPlanInfo($client->getPlanInfo());

        if (
            !isset($tmpVars->cloudSearchTrialInfo)
            || json_decode($tmpVars->cloudSearchTrialInfo, true) !== $trialInfo
        ) {
            $tmpVars->cloudSearchTrialInfo          = json_encode($trialInfo);
            $tmpVars->cloudSearchTrialInfoUpdatedAt = LC_START_TIME;
        }

        return $trialInfo;
    }

    /**
     * Extract trial info from plan info
     *
     * @param $plan
     *
     * @return array|null
     */
    protected function getTrialInfoFromPlanInfo($plan)
    {
        if (isset($plan['trial']) && !$plan['trial']['expired']) {
            $trialInfo = [
                'expired'     => false,
                'expiresSoon' => $plan['trial']['daysLeft'] < self::MIN_TRIAL_DAYS_LEFT_WARNING,
            ];
        } else if (isset($plan['trial'])) {
            $trialInfo = [
                'expired' => true,
            ];
        } else {
            $trialInfo = null;
        }

        return $trialInfo;
    }
}
