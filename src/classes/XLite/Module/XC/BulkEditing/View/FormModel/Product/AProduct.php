<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\FormModel\Product;

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
        $sections = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::getScenarioSections($this->scenario);

        return array_replace([self::SECTION_DEFAULT => []], $sections ?: []);
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = [];

        $fields = \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Scenario::getScenarioFields($this->scenario);

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
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $list = parent::getFormButtons();
        $list['product_list'] = new \XLite\View\Button\SimpleLink(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Back to products'),
                \XLite\View\Button\AButton::PARAM_STYLE => 'action product-list-back-button',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('product_list'),
            )
        );

        return $list;
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
}
