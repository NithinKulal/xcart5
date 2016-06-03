<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Model\Profile;

/**
 * Panel for Anonymous profile form.
 */
class Anonymous extends \XLite\View\StickyPanel\Model\Model
{
    /**
     * Check - sticky panel is active only if form is changed
     *
     * @return boolean
     */
    protected function isFormChangeActivation()
    {
        return false;
    }
}

