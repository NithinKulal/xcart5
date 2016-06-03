<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Profile;

/**
 * Profile abstract form
 */
abstract class AProfile extends \XLite\View\Form\AForm
{
    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $result = parent::getDefaultParams();

        $profileId = $this->getCurrentForm()->getRequestProfileId();

        if ($profileId) {
            $result['profile_id'] = $profileId;
        }

        return $result;
    }

    /**
     * getDefaultClassName
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return 'profile-form';
    }
}
