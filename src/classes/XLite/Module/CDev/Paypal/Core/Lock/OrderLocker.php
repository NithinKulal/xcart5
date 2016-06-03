<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Lock;

use XLite\Module\CDev\Paypal;

/**
 * Order locker
 */
class OrderLocker extends \XLite\Core\Lock\AObjectCacheLocker
{
    /**
     * Lock object
     *
     * @param Object       $object Object
     * @param integer|null $ttl    TTL OPTIONAL
     */
    public function lock($object, $ttl = null)
    {
        parent::lock($object, $ttl);
        Paypal\Main::addLog('Lock order', array('OrderId' => $object->getOrderId()));
    }

    /**
     * Unlock object
     *
     * @param Object $object Object
     */
    public function unlock($object)
    {
        parent::unlock($object);
        Paypal\Main::addLog('Unlock order', array('OrderId' => $object->getOrderId()));
    }

    /**
     * Check locked state
     *
     * @param Object       $object Object
     * @param boolean      $strict Do not check for expiration OPTIONAL
     * @param integer|null $ttl    Time to live in seconds OPTIONAL
     *
     * @return boolean
     */
    public function isLocked($object, $strict = false, $ttl = null)
    {
        $status = parent::isLocked($object, $strict, $ttl);
        Paypal\Main::addLog(
            'Check order lock state',
            array(
                'OrderId' => $object->getOrderId(),
                'status' => $status ? 'locked' : 'unlocked',
            )
        );

        return $status;
    }

    /**
     * @param \XLite\Model\Order $object Order entity
     *
     * @return string
     */
    protected function getIdentifier($object)
    {
        return $object->getOrderId();
    }
}
