<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form;

/**
 * Change attribute values form
 */
class ChangeAttributeValues extends \XLite\View\Form\AForm
{
    /**
     * Widge parameters names
     */
    const PARAM_SOURCE     = 'source';
    const PARAM_STORAGE_ID = 'storage_id';
    const PARAM_ITEM_ID    = 'item_id';

    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'change_attribute_values';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'change';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_SOURCE     => new \XLite\Model\WidgetParam\TypeString('Source', \XLite\Core\Request::getInstance()->source),
            self::PARAM_STORAGE_ID => new \XLite\Model\WidgetParam\TypeInt('Storage id', \XLite\Core\Request::getInstance()->storage_id),
            self::PARAM_ITEM_ID    => new \XLite\Model\WidgetParam\TypeInt('Item id', \XLite\Core\Request::getInstance()->item_id),
        );
    }

    /**
     * Initialization
     *
     * @return void
     */
    protected function initView()
    {
        parent::initView();

        $this->widgetParams[self::PARAM_FORM_PARAMS]->appendValue($this->getFormDefaultParams());
    }

    /**
     * Get form default parameters
     *
     * @return array
     */
    protected function getFormDefaultParams()
    {
        return array(
            'source'     => $this->getParam(self::PARAM_SOURCE),
            'storage_id' => $this->getParam(self::PARAM_STORAGE_ID),
            'item_id'    => $this->getParam(self::PARAM_ITEM_ID),
        );
    }
}
