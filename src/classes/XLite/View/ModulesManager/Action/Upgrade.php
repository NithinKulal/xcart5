<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager\Action;

/**
 * 'Upgrade' action link for Module list (Modules manage)
 *
 * @ListChild (list="itemsList.module.manage.columns.module-main-section.actions", weight="25", zone="admin")
 */
class Upgrade extends \XLite\View\ModulesManager\Action\AAction
{
    /**
     * Update type values
     */
    const TYPE_UPGRADE = 'G';
    const TYPE_UPDATE  = 'D';

    /**
     * @var string Update type
     */
    protected $upgradeType;

    /**
     * Defines the name of the action
     *
     * @return string
     */
    public function getName()
    {
        return 'upgrade-action no-disable ' . ($this->isUpgrade() ? 'upgrade' : 'update');
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'items_list/module/manage/parts/columns/module-main-section/actions/upgrade.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $module = $this->getModule();

        return parent::isVisible()
            && $module->isInstalled()
            && $this->areUpdatesAvailable();
    }

    /**
     * Get button label
     *
     * @return string
     */
    protected function getButtonLabel()
    {
        return $this->isUpgrade ? static::t('Upgrade') : static::t('Update');
    }

    /**
     * Return true if upgrade available
     *
     * @return boolean
     */
    protected function isUpgrade()
    {
        return $this->areUpdatesAvailable(static::TYPE_UPGRADE);
    }

    /**
     * Return true if specific (or any) updates are avaialable
     *
     * @param string $type Type of updates (upgrade or update)
     *
     * @return boolean
     */
    protected function areUpdatesAvailable($type = null)
    {
        $module = $this->getModule();
        $mid = $module->getModuleId();

        if (!isset($this->upgradeType[$mid])) {

            if ($module->getRepository()->getModuleForUpdate($module)) {
                $this->upgradeType[$mid] = static::TYPE_UPGRADE;

            } elseif ($module->getRepository()->getModuleForUpgrade($module)) {
                $this->upgradeType[$mid] = static::TYPE_UPDATE;

            } else {
                $this->upgradeType[$mid] = false;
            }
        }

        return ($type && $type == $this->upgradeType[$mid]) || (bool) $this->upgradeType[$mid];
    }
}
