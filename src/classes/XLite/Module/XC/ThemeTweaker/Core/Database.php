<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core;

/**
 * Database
 */
class Database extends \XLite\Core\Database implements \XLite\Base\IDecorator
{
    /**
     * Get repository
     *
     * @param string $class Entity class name
     *
     * @return \XLite\Model\Repo\ARepo
     */
    public static function getRepo($class)
    {
        $class = static::getEntityClass($class);

        return 'XLite\Module\XC\ThemeTweaker\Model\FlexyTemplate' !== $class
            ? parent::getRepo($class)
            : null;
    }
}
