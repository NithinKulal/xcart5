<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model\Repo;

/**
 * The Store model repository
 */
class Store extends \XLite\Model\Repo\ARepo
{
    /**
     * @return \XLite\Module\XC\MailChimp\Model\Store
     */
    public function getDefaultStore()
    {
        $store = $this->findOneBy([
            'main'  => true,
        ]);
        
        if (!$store) {
            $store = $this->findOneBy([]);
        }

        return $store;   
    }
}
