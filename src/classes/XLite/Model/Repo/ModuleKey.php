<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Module key repository
 */
class ModuleKey extends \XLite\Model\Repo\ARepo
{
    /**
     * Alias
     *
     * @param string $author Module author
     * @param string $name   Module name
     *
     * @return \XLite\Model\ModuleKey
     */
    public function findKey($author, $name)
    {
        return $this->findOneBy(compact('author', 'name'));
    }
}
