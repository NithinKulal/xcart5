<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Profile;

/**
 * Profiel form
 */
class AdminMain extends \XLite\View\Form\Profile\AProfile
{

    /**
     * isRegisterMode
     *
     * @return boolean
     */
    protected function isRegisterMode()
    {
        return $this->getCurrentForm()->isRegisterMode();
    }

    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'profile';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'modify';
    }

    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $result = parent::getDefaultParams();

        if ($this->isRegisterMode()) {
            // Do not pass the profile ID for new profiles
            unset($result['profile_id']);
            // Set the appropriate mode
            $result['mode'] = $this->getCurrentForm()->getRegisterMode();
        }

        return $result;
    }

}
