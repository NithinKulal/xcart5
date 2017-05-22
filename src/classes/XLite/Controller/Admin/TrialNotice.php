<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Trial notice page controller
 */
class TrialNotice extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return page title
     *
     * @return string
     */
    public function getTitle()
    {
        if (\XLite\View\ModulesManager\TrialNotice::getTrialNoticeVersion()
            === \XLite\View\ModulesManager\TrialNotice::TRIAL_NOTICE_VERSION_1
        ) {

            return static::t('Evaluation notice');

        } else {
            if (\XLite::isTrialPeriodExpired()) {

                return static::t('Your X-Cart Business trial has expired!');

            } else {

                return static::t(
                    'X-Cart Business trial will expire in X days',
                    [
                        'count' => \XLite::getTrialPeriodLeft(),
                    ]
                );
            }
        }
    }
}
