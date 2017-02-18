<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Upgrade\PostRebuildActions;

/**
 * Main
 */
class Main extends \Includes\Decorator\Plugin\APlugin
{
    const STEP_TTL = 10;

    /**
     * Execute certain hook handle
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return void
     */
    public function executeHookHandler()
    {
        if (\XLite\Upgrade\Cell::getInstance()->isUpgraded()) {
            $entries = \XLite\Upgrade\Cell::getInstance()->getEntries();
            if ($entries) {
                \Includes\Utils\Operator::showMessage('', true, false);

                /** @var \XLite\Upgrade\Entry\AEntry $entry */
                foreach ($entries as $entry) {
                    if (!$entry->isPostUpgradeActionsCalled()) {
                        if (!$entry->isPostUpgradeActionsStillValid()) {
                            $message = '...Actions can\'t be invoked because entry is not valid: ' . $entry->getActualName();
                            \Includes\Utils\Operator::showMessage(str_replace('\\', '\\\\', $message), true, true);
                            $entry->setPostUpgradeActionsCalled();
                            break;
                        }

                        $message = '...Invoke actions for ' . $entry->getActualName();
                        \Includes\Utils\Operator::showMessage(str_replace('\\', '\\\\', $message), true, true);
                        \Includes\Decorator\Utils\CacheManager::logMessage(PHP_EOL);
                        \Includes\Decorator\Utils\CacheManager::logMessage($message);

                        $isInvoked = \XLite\Upgrade\Cell::getInstance()->runHelper($entry, 'post_rebuild');

                        if ($isInvoked && \XLite\Upgrade\Cell::getInstance()->getHookRedirect()) {
                            break;
                        }

                        if (!\XLite\Upgrade\Cell::getInstance()->hasUnfinishedUpgradeHooks('post_rebuild', $entry)) {
                            // All post-rebuild hooks completed, run the rest actions...
                            \XLite\Upgrade\Cell::getInstance()->runCommonHelper($entry, 'add_labels');
                            \XLite\Upgrade\Cell::getInstance()->callInstallEvent($entry);
                            $entry->setPostUpgradeActionsCalled();
                        }

                        if (\Includes\Decorator\Utils\CacheManager::isTimeExceeds(static::STEP_TTL)) {
                            break;
                        }
                    }
                }
            }
        }

        \Includes\Decorator\Utils\CacheManager::logMessage(PHP_EOL);

        \XLite\Core\Database::getEM()->flush();
        \XLite\Core\Database::getEM()->clear();
    }
}
