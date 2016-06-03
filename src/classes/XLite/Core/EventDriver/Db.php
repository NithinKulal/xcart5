<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\EventDriver;

/**
 * DB-based event driver 
 */
class Db extends \XLite\Core\EventDriver\AEventDriver
{
    /**
     * Get driver code
     *
     * @return string
     */
    public static function getCode()
    {
        return 'db';
    }

    /**
     * Fire event
     *
     * @param string $name      Event name
     * @param array  $arguments Arguments OPTIONAL
     *
     * @return boolean
     */
    public function fire($name, array $arguments = array())
    {
        $entity = new \XLite\Model\EventTask;
        $entity->setName($name);
        $entity->setArguments($arguments);

        \XLite\Core\Database::getEM()->persist($entity);
        \XLite\Core\Database::getEM()->flush();
    }

}

