<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Plugin\PrepareDBSchema;

use Includes\Decorator\Plugin\Doctrine\Utils\SchemaMigrationManager;
use Includes\Decorator\Plugin\Doctrine\Utils\EntityManager;
use Includes\Decorator\Utils\CacheInfo;
use Includes\Decorator\Utils\CacheManager;

/**
 * Main
 */
class Main extends \Includes\Decorator\Plugin\Doctrine\Plugin\APlugin
{
    /**
     * Execute certain hook handle
     *
     * @return void
     */
    public function executeHookHandler()
    {
        $this->saveMetadata();

        if (!CacheManager::isCapsular() || $this->isMetadataChanged()) {
            SchemaMigrationManager::createMigration();

        } else {
            SchemaMigrationManager::createEmptyMigration();
        }
    }

    /**
     * Save current metadata 
     * 
     * @return void
     */
    protected function saveMetadata()
    {
        CacheInfo::set(
            'metadata',
            EntityManager::getAllMetadata()
        );
    }

    /**
     * Check - metadata is changed or not
     * 
     * @return boolean
     */
    protected function isMetadataChanged()
    {
        $previous = CacheInfo::get('metadata', false);

        if (!$previous) {
            $result = true;

        } else {
            $currentHash = $this->getMetadataHash(CacheInfo::get('metadata'));
            $previousHash = $this->getMetadataHash($previous);

            CacheInfo::set(
                'metadataHashes',
                array(
                    'current'  => $currentHash,
                    'previous' => $previousHash,
                )
            );
            $result = $currentHash != $previousHash;
        }

        return $result;
    }

    /**
     * Get metadata hash 
     * 
     * @param array $metadatas Metadata list
     *  
     * @return string
     */
    protected function getMetadataHash(array $metadatas)
    {
        $hashs = array();
        foreach ($metadatas as $metadata) {
            $hashs[] = md5(serialize($metadata));
        }

        return md5(implode('', $hashs));
    }
}
