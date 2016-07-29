<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\FormModel\Product;

use XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario;

abstract class AProduct extends \XLite\View\FormModel\AFormModel
{
    protected $scenario = '';

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/BulkEditing/form_model/controller.js';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/BulkEditing/form_model/product.css';

        return $list;
    }

    /**
     * Do not render form_start and form_end in null returned
     *
     * @return string|null
     */
    protected function getTarget()
    {
        return 'bulk_edit';
    }

    /**
     * @return string
     */
    protected function getAction()
    {
        return 'bulk_edit';
    }

    /**
     * @return array
     */
    protected function getActionParams()
    {
        return ['scenario' => $this->scenario];
    }

    /**
     * @return array
     */
    protected function defineSections()
    {
        $sections = Scenario::getScenarioSections($this->scenario);

        return array_replace(parent::defineSections(), $sections ?: []);
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = [];

        $fields = Scenario::getScenarioFields($this->scenario);

        foreach ($fields as $section => $sectionFields) {
            $sectionSchema = [];
            foreach ($sectionFields as $name => $field) {
                $sectionSchema = array_merge(
                    $sectionSchema,
                    call_user_func([$field['class'], 'getSchema'], $name, $field['options'])
                );
            }
            $schema[$section] = $sectionSchema;
        }

        return $schema;
    }

    /**
     * Return form theme files. Used in template.
     *
     * @return array
     */
    protected function getFormThemeFiles()
    {
        $list = parent::getFormThemeFiles();
        $list[] = 'modules/XC/BulkEditing/form_model/bulk_edit_theme.twig';

        return $list;
    }

    /**
     * Returns scenario current data view
     *
     * @return string
     */
    protected function getScenarioView()
    {
        return Scenario::getScenarioView($this->getCurrentScenario()) ?: '';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return 'form_model.xc.bulkediting.product';
    }
}
