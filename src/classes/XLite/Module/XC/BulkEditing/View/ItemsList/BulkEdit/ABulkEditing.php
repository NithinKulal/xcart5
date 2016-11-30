<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\ItemsList\BulkEdit;

/**
 * Abstract product list
 */
abstract class ABulkEditing extends \XLite\View\ItemsList\Model\Table
{
    /**
     * @var string
     */
    protected $scenario = '';

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/BulkEditing/items_list/selected/controller.js';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $result = [];
        $sectionIndex = 0;

        $fields = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::getScenarioFields($this->scenario);
        foreach ($fields as $section => $sectionFields) {
            ++$sectionIndex;
            foreach ($sectionFields as $name => $field) {
                $options = $field['options'];
                $position = $options['position'];
                $options['position'] = $sectionIndex . str_repeat('0', 10 - strlen($position)) . (string) $position;
                $result = array_merge(
                    $result,
                    call_user_func([$field['class'], 'getViewColumns'], $name, $options)
                );
            }
        }

        return $result;
    }

    /**
     * Get entity value
     *
     * @param \XLite\Model\AEntity $entity Entity object
     * @param string               $name   Property name
     *
     * @return mixed
     */
    protected function getEntityValue($entity, $name)
    {
        $result = '';

        $fields = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::getScenarioFields($this->scenario);
        foreach ($fields as $section => $sectionFields) {
            if (isset($sectionFields[$name])) {
                $result = call_user_func(
                    [$sectionFields[$name]['class'], 'getViewValue'],
                    $name,
                    $entity
                );

                break;
            }
        }

        return $result;
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return null;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        // $cnd = parent::getSearchCondition();

        $sessionCellName = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::$searchCndSessionCell;
        $filter = \XLite\Core\Session::getInstance()->{$sessionCellName};

        return $filter['selected']
            ? new \XLite\Core\CommonCell(['ids' => $filter['selected']])
            : \XLite\Core\Session::getInstance()->{$filter['conditionCell']};
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' bulk-edit-selected';
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $list = parent::getCommonParams();
        $list['scenario'] = $this->scenario;

        return $list;
    }
}
