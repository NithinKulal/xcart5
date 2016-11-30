<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * \XLite\View\FormField\Select\LayoutType
 */
class LayoutType extends \XLite\View\FormField\Select\Regular
{
    const PARAM_AVAILABLE_TYPES = 'availableTypes';
    const PARAM_LAYOUT_GROUP = 'layoutGroup';

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/layout_type.css';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/layout_type.js';

        return $list;
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            \XLite\Core\Layout::LAYOUT_TWO_COLUMNS_LEFT  => static::t('Two columns with left sidebar'),
            \XLite\Core\Layout::LAYOUT_TWO_COLUMNS_RIGHT => static::t('Two columns with right sidebar'),
            \XLite\Core\Layout::LAYOUT_THREE_COLUMNS     => static::t('Three columns'),
            \XLite\Core\Layout::LAYOUT_ONE_COLUMN        => static::t('One column'),
        );
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $availableTypes = $this->getParam(static::PARAM_AVAILABLE_TYPES);
        $options = parent::getOptions();

        $result = array();
        foreach ($options as $type => $label) {
            if (in_array($type, $availableTypes, true)) {
                $result[$type] = $label;
            }
        }

        return $result;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'layout_type.twig';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_AVAILABLE_TYPES => new \XLite\Model\WidgetParam\TypeCollection(
                'Available types',
                array(),
                false
            ),
            self::PARAM_LAYOUT_GROUP => new \XLite\Model\WidgetParam\TypeString(
                'Layout group',
                \XLite\Core\Layout::LAYOUT_GROUP_DEFAULT
            ),
        );
    }

    /**
     * Get option classes
     *
     * @param mixed $value Value
     * @param mixed $text  Text
     *
     * @return array
     */
    protected function getOptionClasses($value, $text)
    {
        $result = 'layout-type ' . $value;
        if ($this->isOptionSelected($value)) {
            $result .= ' selected';
        }

        return $result;
    }

    protected function getLayoutGroup()
    {
        return $this->getParam(static::PARAM_LAYOUT_GROUP);
    }

    /**
     * Returns layout type image
     *
     * @param string $value Layout type
     *
     * @return string
     */
    protected function getImage($value)
    {
        return $this->getSVGImage('images/layout/' . $value . '.svg');
    }

    /**
     * Returns layout preview for given type
     *
     * @param string $value Layout type
     *
     * @return string
     */
    protected function getPreviewImage($value)
    {
        return \XLite\Core\Layout::getInstance()->getLayoutPreview(
            \XLite\Core\Database::getRepo('XLite\Model\Module')->getCurrentSkinModule(),
            \XLite\Core\Layout::getInstance()->getLayoutColor(),
            $value
        );
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && 1 < count($this->getParam(static::PARAM_AVAILABLE_TYPES));
    }
}
