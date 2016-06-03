<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Model\EntityVersion;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

/**
 * Entity version updater subscribes to Doctrine events to update entity version UUIDs automatically on persist and update actions.
 */
class EntityVersionUpdater implements EventSubscriber
{
    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $instance = $eventArgs->getEntity();

        if ($instance instanceof EntityVersionInterface) {
            $instance->setEntityVersion($this->generateUUIDv4());
        }
    }

    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $instance = $eventArgs->getEntity();

        if ($instance instanceof EntityVersionInterface) {
            $instance->setEntityVersion($this->generateUUIDv4());
        }
    }

    protected function generateUUIDv4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}