<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Dropdown;

/**
 * Add attribute
 */
class AddAttribute extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * Widget parameter names
     */
    const PARAM_LIST_ID = 'listId';

    /**
     * Return button text
     *
     * @return string
     */
    protected function getButtonLabel()
    {
        return static::t('Add attribute');
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_LIST_ID => new \XLite\Model\WidgetParam\TypeInt('List ID', 0),
        ];
    }

    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = [];
        $position = 0;

        foreach (\XLite\Model\Attribute::getTypes() as $type => $name) {
            $list[$type] = [
                'params'   => [
                    'label'  => $name,
                    'style'  => 'action link list-action',
                    'jsCode' => 'addAttribute(\'' . $type . '\',' . $this->getParam(static::PARAM_LIST_ID) . ')',
                ],
                'position' => $position += 100,
            ];
        }

        return $list;
    }

    /**
     * Get style
     *
     * @return string
     */
    protected function getStyle()
    {
        return parent::getStyle() . ' use-first-item';
    }
}
