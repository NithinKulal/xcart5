<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Upgrade\PostprocessPostRebuildActions;

/**
 * Main
 */
class Main extends \Includes\Decorator\Plugin\APlugin
{
    /**
     * Execute certain hook handle
     *
     * @return void
     */
    public function executeHookHandler()
    {
        if (\XLite\Upgrade\Cell::getInstance()->isUpgraded()
            && $this->hasUncalledActions()
        ) {
            \Includes\Decorator\Utils\CacheManager::$skipStepCompletion = true;
        }
    }

    /**
     * Check for uncalled actions
     *
     * @return boolean
     */
    protected function hasUncalledActions()
    {
        $result = false;
        $entries = \XLite\Upgrade\Cell::getInstance()->getEntries();

        if ($entries) {
            /** @var \XLite\Upgrade\Entry\AEntry $entry */
            foreach ($entries as $entry) {
                if (!$entry->isPostUpgradeActionsCalled()) {
                    $result = true;

                    break;
                }
            }
        }

        return $result;
    }
}
