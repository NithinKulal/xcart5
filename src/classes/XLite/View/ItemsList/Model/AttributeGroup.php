<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Attribute groups items list
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class AttributeGroup extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('attribute_groups'));
    }

    /**
     * Return additional panel style
     *
     * @return string
     */
    protected function getAdditionalPanelStyle()
    {
        $result = 'additional-panel';

        if (0 == \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->count()) {
            $result .= ' hidden';
        }

        return $result;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'attribute_groups/style.css';

        return $list;
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'attribute_groups';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            array(
                'product_class_id' => intval(\XLite\Core\Request::getInstance()->product_class_id),
            )
        );
    }

    /**
     * Get bottom actions
     *
     * @return array
     */
    protected function getBottomActions()
    {
        $actions = parent::getBottomActions();

        $actions[] = 'attribute_groups/submit.twig';

        return $actions;
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
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\AttributeGroup';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('attribute_group');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New group';
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

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' attribute_groups';
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

        $entity->setProductClass($this->getProductClass());

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

        $result->productClass = $this->getProductClass();

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
