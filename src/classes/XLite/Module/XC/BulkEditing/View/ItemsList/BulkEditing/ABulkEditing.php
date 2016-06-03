<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\ItemsList\BulkEditing;

/**
 * Abstract product list
 */
abstract class ABulkEditing extends \XLite\View\ItemsList\AItemsList
{
    protected $scenario = '';

    abstract protected function getRepo();

    abstract protected function getItemName($item);

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/BulkEditing/items_list/selected/style.css';

        return $list;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/BulkEditing/items_list/selected/controller.js';

        return $list;
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
     * Return name of the base widgets list
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.bulk-edit';
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/BulkEditing/items_list/selected';
    }

    /**
     * Return "empty list" catalog
     *
     * @return string
     */
    protected function getEmptyListDir()
    {
        return $this->getDir() . '/' . $this->getPageBodyDir();
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return null;
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

    /**
     * Return modules list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return $this->getRepo()->search($cnd, $countOnly);
    }

    protected function getScenarioFields($item)
    {
        return $this->defineFields($item);
    }

    /**
     * @return array
     */
    protected function defineFields($item)
    {
        $schema = [];

        $sections = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::getScenarioSections($this->scenario);
        $fields = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::getScenarioFields($this->scenario);

        foreach ($fields as $section => $sectionFields) {
            $sectionSchema = [];
            foreach ($sectionFields as $name => $field) {
                $sectionSchema = array_merge(
                    $sectionSchema,
                    call_user_func([$field['class'], 'getViewData'], $name, $item, $field['options'])
                );
            }

            if ($sectionSchema) {
                $schema[$section] = [
                    'fields' => $this->sortItems($sectionSchema),
                ];

                if ($sections[$section]) {
                    $sectionLabel = isset($sections[$section]['label'])
                        ? $sections[$section]['label']
                        : $sections[$section];

                    if ($sectionLabel) {
                        $schema[$section]['label'] = $sectionLabel;
                    }
                }
            }
        }

        return $schema;
    }

    protected function sortItems($items)
    {
        $result = [];
        $position = 0;

        /**
         * @var int|string   $name
         * @var array|string $schema
         */
        foreach ((array) $items as $name => $schema) {
            if (array_key_exists('position', $schema) && is_numeric($schema['position'])) {
                $position = (int) $schema['position'];

            } else {
                $schema['position'] = $position += 0.001;
            }

            $result[$name] = $schema;
        }

        uasort($result, function ($a, $b) {
            $a = (float) $a['position'];
            $b = (float) $b['position'];

            return $a === $b ? 0 : ($a > $b ? 1 : -1);
        });

        return $result;
    }
}
