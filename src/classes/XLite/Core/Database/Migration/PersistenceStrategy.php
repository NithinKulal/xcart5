<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\Database\Migration;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Includes\Database\Migration\Migration;

/**
 * Migration-aware PersistenceStrategy checks if the changes contained in the unit of work can be persisted. If changes are incompatible with the ongoing migration, exception is raised.
 */
class PersistenceStrategy implements EventSubscriber
{
    /**
     * @var PersistencePolicy
     */
    private $persistencePolicy;

    public function __construct(Migration $migration)
    {
        $this->persistencePolicy = new PersistencePolicy($migration->getMigrationType());
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * postFlush event handler
     *
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $em  = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        if (!$this->persistencePolicy->isPersistencePermitted($em, $uow)) {
            throw new UnsupportedDatabaseOperationDuringMaintenanceException();
        }
    }
}