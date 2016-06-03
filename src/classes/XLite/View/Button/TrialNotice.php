<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Trial notice popup button
 */
class TrialNotice extends \XLite\View\Button\APopupButton
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/trial_notice.js';

        return $list;
    }

    /**
     * Register CSS files
     * TODO: should be loaded in popup; remove after loading will be fixed
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'trial_notice/css/style.css';

        return $list;
    }

    /**
     * Return content for popup button
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Evaluation notice';
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target' => 'trial_notice',
            'widget' => '\XLite\View\ModulesManager\TrialNotice',
            'returnUrl' => \XLite\Core\URLManager::getCurrentURL(),
        );
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' force-notice';
    }
}
