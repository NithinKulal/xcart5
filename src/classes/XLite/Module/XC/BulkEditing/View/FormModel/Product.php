<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\FormModel;

use XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario;

/**
 * @ListChild (list="admin.center", zone="admin")
 */
class Product extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'bulk_edit';

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/BulkEditing/bulk_edit/style.css';
        
        return $list;
    }

    /**
     * @return string
     */
    public function getCurrentScenario()
    {
        return \XLite::getController()->getCurrentScenario();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/BulkEditing/bulk_edit/page.twig';
    }

    /**
     * Returns scenario form model
     *
     * @return string
     */
    protected function getScenarioFormModel()
    {
        return Scenario::getScenarioFormModel($this->getCurrentScenario()) ?: '';
    }

    /**
     * Check scenario progress
     *
     * @return mixed
     */
    protected function isBulkEditNotFinished()
    {
        return \XLite::getController()->isBulkEditNotFinished();
    }
}
