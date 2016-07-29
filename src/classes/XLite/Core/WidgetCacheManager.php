<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use XLite\Core\DependencyInjection\ContainerAwareTrait;

/**
 * Widget cache manager serves as a factory of WidgetCache and also as an implementation of WidgetCacheRegistryInterface.
 *
 * TODO: current WidgetCacheRegistryInterface implementation is prone to race conditions, rewrite without separate registry file (for example, deleteAll can just remove cache entries having certain prefix).
 */
class WidgetCacheManager implements WidgetCacheRegistryInterface
{
    use ContainerAwareTrait;

    /**
     * Delete all
     *
     * @return boolean
     */
    public function deleteAll()
    {
        return $this->getContainer()->get('widget_cache')->deleteAll();
    }

    /**
     * Invalidate widget cache based on entity types that were changed (inserted, updated or removed) during the current request.
     */
    public function invalidateBasedOnDatabaseChanges()
    {
        $notAffectingEntities = [
            'XLite\Model\TmpVar',
            'XLite\Model\Module',
            'XLite\Model\ModuleKey',
            'XLite\Model\Payment\Transaction',
            'XLite\Model\NotificationTranslation',
            'XLite\Model\Notification',
            'XLite\Model\ConfigTranslation',
        ];

        $entityTypes = \XLite\Core\Database::getRepo('XLite\Model\EntityTypeVersion')->getBumpedEntityTypes();

        if (array_diff($entityTypes, $notAffectingEntities)) {
            $this->deleteAll();
        }
    }
}
