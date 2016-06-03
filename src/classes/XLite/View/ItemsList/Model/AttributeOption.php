<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Attribute options items list
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class AttributeOption extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('attribute', 'attribute_options'));
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'attribute_options/style.css';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'name' => array(
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_MAIN     => true,
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_PARAMS   => array('required' => true),
                static::COLUMN_ORDERBY  => 100,
            ),
            'addToNew' => array(
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\AddToNew',
                static::COLUMN_ORDERBY  => 200,
            ),
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\AttributeOption';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('attribute_option');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New value';
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' attribute_options';
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
     * Create entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();

        $entity->setAttribute($this->getAttribute());

        return $entity;
    }

    /**
     * Check - pager box is visible or not
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return false;
    }

    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array();
    }

    /**
     * Return params list to use for search
     * TODO refactor
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ('' !== $paramValue && 0 !== $paramValue) {
                $result->$modelParam = $paramValue;
            }
        }

        $result->attribute = $this->getAttribute();

        return $result;
    }

    // }}}

    /**
     * Return true if param value may contain anything
     *
     * @param string $name Param name
     *
     * @return boolean
     */
    protected function isParamTrusted($name)
    {
        $result = parent::isParamTrusted($name);

        if (!$result && $name === 'name') {
            $result = true;
        }

        return $result;
    }
}
