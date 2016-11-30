<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\MembershipsQuickData\Step;

/**
 * Products
 */
class Products extends \XLite\Logic\MembershipsQuickData\Step\AStep
{
    // {{{ Row processing

    /**
     * Process model
     *
     * @param \XLite\Model\Product $model Product
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        foreach ($this->generator->getMemberships() as $membership) {
            \XLite\Core\QuickData::getInstance()->updateData($model, $membership);
        }
    }

    // }}}

    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product');
    }

    // }}}
}
