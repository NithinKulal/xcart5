<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

use XLite\Core\MagicMethodsIntrospectionInterface;

/**
 * Abstract admin model-based items list (table)
 */
abstract class Table extends \XLite\View\ItemsList\Model\AModel
{
    const COLUMN_NAME          = 'name';
    const COLUMN_TEMPLATE      = 'template';
    const COLUMN_HEAD_TEMPLATE = 'headTemplate';
    const COLUMN_HEAD_HELP     = 'headHelp';
    const COLUMN_SUBHEADER     = 'subheader';
    const COLUMN_CLASS         = 'class';
    const COLUMN_CODE          = 'code';
    const COLUMN_LINK          = 'link';
    const COLUMN_METHOD_SUFFIX = 'methodSuffix';
    const COLUMN_CREATE_CLASS  = 'createClass';
    const COLUMN_CREATE_TEMPLATE = 'createTemplate';
    const COLUMN_MAIN          = 'main';
    const COLUMN_SERVICE       = 'service';
    const COLUMN_PARAMS        = 'params';
    const COLUMN_SORT          = 'sort';
    const COLUMN_SEARCH_WIDGET = 'searchWidget';
    const COLUMN_NO_WRAP       = 'noWrap';
    const COLUMN_EDIT_ONLY     = 'editOnly';
    const COLUMN_SELECTOR      = 'columnSelector';
    const COLUMN_REMOVE        = 'columnRemove';
    const COLUMN_ORDERBY       = 'orderBy';
    const COLUMN_EDIT_LINK     = 'editLink';

    /**
     * Widget param names
     */
    const PARAM_WRAP_WITH_FORM = 'wrapWithForm';

    /**
     * Columns (local cache)
     *
     * @var array
     */
    protected $columns;

    /**
     * Main column index
     *
     * @var integer
     */
    protected $mainColumn;

    /**
     * Define columns structure
     *
     * @return array
     */
    abstract protected function defineColumns();

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_WRAP_WITH_FORM => new \XLite\Model\WidgetParam\TypeBool(
                'Wrap with form',
                $this->wrapWithFormByDefault()
            ),
        );
    }

    /**
     * Get this object
     *
     * @return \XLite\View\ItemsList\Model\Table
     */
    protected function getItemsListObject()
    {
        return $this;
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function isWrapWithForm()
    {
        return $this->getParam(static::PARAM_WRAP_WITH_FORM);
    }

    /**
     * Default value for PARAM_WRAP_WITH_FORM
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return false;
    }

    /**
     * Return wrapper form options
     *
     * @return string
     */
    protected function getFormOptions()
    {
        return array(
            'class'     => '\XLite\View\Form\ItemsList\AItemsList',
            'name'      => str_replace('\\', '', get_class($this)),
            'target'    => $this->getFormTarget(),
            'action'    => 'updateItemsList',
            'params'    => $this->getFormParams(),
        );
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'simple_items_list_controller';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        return array(
            'itemsList' => get_class($this),
        );
    }

    /**
     * Sorting helper for uasort
     *
     * @param array $column1
     * @param array $column2
     *
     * @return boolean
     */
    public function sortColumnsByOrder($column1, $column2)
    {
        $column1[static::COLUMN_ORDERBY] = isset($column1[static::COLUMN_ORDERBY])
            ? $column1[static::COLUMN_ORDERBY]
            : 0;
        $column2[static::COLUMN_ORDERBY] = isset($column2[static::COLUMN_ORDERBY])
            ? $column2[static::COLUMN_ORDERBY]
            : 0;

        return $column1[static::COLUMN_ORDERBY] > $column2[static::COLUMN_ORDERBY];
    }

    /**
     * The columns are ordered according the static::COLUMN_ORDERBY values
     *
     * @return array
     */
    protected function prepareColumns()
    {
        $columns = $this->defineColumns();
        $index = 100;
        foreach ($columns as $i => $v) {
            if (!isset($v[static::COLUMN_ORDERBY])) {
                $columns[$i][static::COLUMN_ORDERBY] = $index;
                $index += 100;
            }
        }

        uasort($columns, array($this, 'sortColumnsByOrder'));

        return $columns;
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/style.css';

        if (static::SORT_TYPE_MOVE === $this->getSortableType()) {
            $list = array_merge(
                $list,
                $this->getWidget(array(), $this->getMovePositionWidgetClassName())->getCSSFiles(),
                $this->getWidget(array(), $this->getOrderByWidgetClassName())->getCSSFiles()
            );
        }

        return $list;
    }

    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/controller.js';

        if (static::SORT_TYPE_MOVE === $this->getSortableType()) {
            $list = array_merge(
                $list,
                $this->getWidget(array(), $this->getMovePositionWidgetClassName())->getJSFiles(),
                $this->getWidget(array(), $this->getOrderByWidgetClassName())->getJSFiles()
            );
        }

        return $list;
    }

    /**
     * Return true if items list should be displayed in static mode (no editable widgets, no controls)
     *
     * @return boolean
     */
    protected function isStatic()
    {
        return false;
    }

    /**
     * Check - pager box is visible or not
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return parent::isPagerVisible()
            && $this->getPager()->isVisible();
    }

    /**
     * Get preprocessed columns structure
     *
     * @return array
     */
    protected function getColumns()
    {
        if (!isset($this->columns)) {
            $this->columns = array();

            if ($this->getLeftActions()) {
                $this->columns[] = array(
                    static::COLUMN_CODE     => 'actions left',
                    static::COLUMN_NAME     => '',
                    static::COLUMN_TEMPLATE => 'items_list/model/table/left_actions.twig',
                    static::COLUMN_SERVICE  => true,
                    static::COLUMN_HEAD_TEMPLATE => static::SORT_TYPE_INPUT === $this->getSortType()
                        ? 'items_list/model/table/parts/pos_input.twig'
                        : (static::SORT_TYPE_MOVE === $this->getSortType()
                            ? 'items_list/model/table/parts/pos_move.twig'
                            : ''
                        ),
                    static::COLUMN_SELECTOR => $this->isSelectable(),
                );
            }

            foreach ($this->prepareColumns() as $idx => $column) {
                $column[static::COLUMN_CODE] = $idx;
                $column[static::COLUMN_METHOD_SUFFIX]
                    = \XLite\Core\Converter::convertToCamelCase($column[static::COLUMN_CODE]);
                if (!isset($column[static::COLUMN_TEMPLATE]) && !isset($column[static::COLUMN_CLASS])) {
                    $column[static::COLUMN_TEMPLATE] = 'items_list/model/table/field.twig';
                }
                $column[static::COLUMN_PARAMS] = isset($column[static::COLUMN_PARAMS])
                    ? $column[static::COLUMN_PARAMS]
                    : array();
                $this->columns[] = $column;
            }

            if ($this->getEditLink()) {
                $this->columns[] = array(
                    static::COLUMN_CODE     => 'edit-link',
                    static::COLUMN_NAME     => '',
                    static::COLUMN_TEMPLATE => 'items_list/model/table/parts/edit_link.twig',
                    static::COLUMN_SERVICE  => true,
                    static::COLUMN_LINK     => $this->getEditLink(),
                );
            }

            if ($this->getRightActions()) {
                $this->columns[] = array(
                    static::COLUMN_CODE     => 'actions right',
                    static::COLUMN_NAME     => '',
                    static::COLUMN_TEMPLATE => 'items_list/model/table/right_actions.twig',
                    static::COLUMN_SERVICE  => true,
                    static::COLUMN_REMOVE   => $this->isRemoved(),
                );
            }
        }

        return $this->columns;
    }

    /**
     * Return columns count
     *
     * @return integer
     */
    protected function getColumnsCount()
    {
        return count($this->getColumns());
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isTableHeaderVisible()
    {
        $result = false;
        foreach ($this->getColumns() as $column) {
            if (!empty($column['name'])) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Get main column
     *
     * @return array
     */
    protected function getMainColumn()
    {
        $columns = $this->getColumns();

        if (!isset($this->mainColumn)) {
            $result = null;
            $first = null;

            foreach ($columns as $i => $column) {
                if (!isset($column[static::COLUMN_SERVICE]) || !$column[static::COLUMN_SERVICE]) {
                    if (!isset($first)) {
                        $first = $i;
                    }
                    if (isset($column[static::COLUMN_MAIN]) && $column[static::COLUMN_MAIN]) {
                        $result = $i;
                        break;
                    }
                }
            }

            $this->mainColumn = isset($result) ? $result : $first;
        }

        return isset($columns[$this->mainColumn]) ? $columns[$this->mainColumn] : null;
    }

    /**
     * Check - specified column is main or not
     *
     * @param array $column Column
     *
     * @return boolean
     */
    protected function isMainColumn(array $column)
    {
        $main = $this->getMainColumn();

        return $main && $column[static::COLUMN_CODE] == $main[static::COLUMN_CODE];
    }

    /**
     * Get column value
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model
     *
     * @return mixed
     */
    protected function getColumnValue(array $column, \XLite\Model\AEntity $entity)
    {
        $suffix = $column[static::COLUMN_METHOD_SUFFIX];

        // Getter
        $method = 'get' . $suffix . 'ColumnValue';
        $value = method_exists($this, $method)
            ? $this->$method($entity)
            : $this->getEntityValue($entity, $column[static::COLUMN_CODE]);

        // Preprocessing
        $method = 'preprocess' . \XLite\Core\Converter::convertToCamelCase($column[static::COLUMN_CODE]);
        if (method_exists($this, $method)) {
            // $method assembled frm 'preprocess' + field name
            $value = $this->$method($value, $column, $entity);
        }

        return $value;
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
        $result = null;

        $method = 'get' . \XLite\Core\Converter::convertToCamelCase($name);

        if (method_exists($entity, $method)
            || ($entity instanceof MagicMethodsIntrospectionInterface && $entity->hasMagicMethod($method))
        ) {
            // $method assembled frm 'get' + field name
            $result = $entity->$method();

        } elseif ($entity->isPropertyExists($name)) {
            $result = $entity->$name;
        }

        return $result;
    }

    /**
     * Get field objects list (only inline-based form fields)
     *
     * @return array
     */
    protected function getFieldObjects()
    {
        $list = array();

        foreach ($this->getColumns() as $column) {
            $name = $column[static::COLUMN_CODE];
            if (isset($column[static::COLUMN_CLASS])
                && is_subclass_of($column[static::COLUMN_CLASS], 'XLite\View\FormField\Inline\AInline')
            ) {
                $params = isset($column[static::COLUMN_PARAMS]) ? $column[static::COLUMN_PARAMS] : array();
                $list[] = array(
                    'class'      => $column[static::COLUMN_CLASS],
                    'parameters' => array('fieldName' => $name, 'fieldParams' => $params),
                );
            }
        }

        if ($this->isSwitchable()) {
            $cell = $this->getSwitcherField();
            $list[] = array(
                'class'      => $cell['class'],
                'parameters' => array('fieldName' => $cell['name'], 'fieldParams' => $cell['params']),
            );
        }

        if (static::SORT_TYPE_NONE != $this->getSortType()) {
            $cell = $this->getSortField();
            $list[] = array(
                'class'      => $cell['class'],
                'parameters' => array('fieldName' => $cell['name'], 'fieldParams' => $cell['params']),
            );
        }

        foreach ($list as $i => $class) {
            $list[$i] = new $class['class']($class['parameters']);
        }

        return $list;
    }

    /**
     * Get switcher field
     *
     * @return array
     */
    protected function getSwitcherField()
    {
        return array(
            'class'  => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\Enabled',
            'name'   => 'enabled',
            'params' => array(),
        );
    }

    /**
     * Get sort field
     *
     * @return array
     */
    protected function getSortField()
    {
        return static::SORT_TYPE_INPUT == $this->getSortType()
            ? array(
                'class'  => $this->getOrderByWidgetClassName(),
                'name'   => 'position',
                'params' => array(),
            )
            :
            array(
                'class'  => $this->getMovePositionWidgetClassName(),
                'name'   => 'position',
                'params' => array(),
            );
    }

    /**
     * Defines the position MOVE widget class name
     *
     * @return string
     */
    protected function getMovePositionWidgetClassName()
    {
        return 'XLite\View\FormField\Inline\Input\Text\Position\Move';
    }

    /**
     * Defines the position OrderBy widget class name
     *
     * @return string
     */
    protected function getOrderByWidgetClassName()
    {
        return 'XLite\View\FormField\Inline\Input\Text\Position\OrderBy';
    }

    /**
     * Get create field classes
     *
     * @return array
     */
    protected function getCreateFieldClasses()
    {
        $list = array();

        foreach ($this->getColumns() as $column) {
            $name = $column[static::COLUMN_CODE];
            $class = null;
            if (isset($column[static::COLUMN_CREATE_CLASS])
                && is_subclass_of($column[static::COLUMN_CREATE_CLASS], 'XLite\View\FormField\Inline\AInline')
            ) {
                $class = $column[static::COLUMN_CREATE_CLASS];

            } elseif (isset($column[static::COLUMN_CLASS])
                && is_subclass_of($column[static::COLUMN_CLASS], 'XLite\View\FormField\Inline\AInline')
            ) {
                $class = $column[static::COLUMN_CLASS];
            }

            if ($class) {
                $params = isset($column[static::COLUMN_PARAMS]) ? $column[static::COLUMN_PARAMS] : array();
                $list[] = array(
                    'class'      => $class,
                    'parameters' => array(
                        \XLite\View\FormField\Inline\AInline::PARAM_FIELD_NAME   => $name,
                        \XLite\View\FormField\Inline\AInline::PARAM_FIELD_PARAMS => $params,
                        \XLite\View\FormField\Inline\AInline::PARAM_EDIT_ONLY    => true,
                    ),
                );
            }
        }

        foreach ($list as $i => $class) {
            $list[$i] = new $class['class']($class['parameters']);
        }

        return $list;
    }

    /**
     * Get create line columns
     *
     * @return array
     */
    protected function getCreateColumns()
    {
        $columns = array();

        if ($this->getLeftActions()) {
            $columns[] = array(
                static::COLUMN_CODE     => 'actions left',
                static::COLUMN_NAME     => '',
                static::COLUMN_SERVICE  => true,
                static::COLUMN_TEMPLATE => 'items_list/model/table/parts/empty_left.twig',
            );
        }

        foreach ($this->prepareColumns() as $idx => $column) {
            if ((isset($column[static::COLUMN_CREATE_CLASS]) && $column[static::COLUMN_CREATE_CLASS])
                || (isset($column[static::COLUMN_CLASS]) && $column[static::COLUMN_CLASS])
            ) {
                // By class
                $column[static::COLUMN_CODE] = $idx;
                $column[static::COLUMN_METHOD_SUFFIX]
                    = \XLite\Core\Converter::convertToCamelCase($column[static::COLUMN_CODE]);
                if (!isset($column[static::COLUMN_CREATE_CLASS]) || !$column[static::COLUMN_CREATE_CLASS]) {
                    $column[static::COLUMN_CREATE_CLASS] = $column[static::COLUMN_CLASS];
                }
                $columns[] = $column;

            } elseif (!empty($column[static::COLUMN_CREATE_TEMPLATE])) {
                // By template
                $columns[] = array(
                    static::COLUMN_CODE     => $idx,
                    static::COLUMN_TEMPLATE => $column[static::COLUMN_CREATE_TEMPLATE],
                );

            } else {
                // Empty
                $columns[] = array(
                    static::COLUMN_CODE     => $idx,
                    static::COLUMN_TEMPLATE => 'items_list/model/table/empty.twig',
                );
            }
        }

        if ($this->getRightActions()) {
            $columns[] = array(
                static::COLUMN_CODE     => 'actions right',
                static::COLUMN_NAME     => '',
                static::COLUMN_SERVICE  => true,
                static::COLUMN_TEMPLATE => $this->isRemoved()
                    ? 'items_list/model/table/parts/remove_create.twig'
                    : 'items_list/model/table/parts/empty_right.twig',
            );
        }

        return $columns;
    }

    /**
     * List has top creation box
     *
     * @return boolean
     */
    protected function isTopInlineCreation()
    {
        return static::CREATE_INLINE_TOP === $this->isInlineCreation();
    }

    /**
     * List has bottom creation box
     *
     * @return boolean
     */
    protected function isBottomInlineCreation()
    {
        return static::CREATE_INLINE_BOTTOM === $this->isInlineCreation();
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Table';
    }

    /**
     * Get cell list name part
     *
     * @param string $type   Cell type
     * @param array  $column Column
     *
     * @return string
     */
    protected function getCellListNamePart($type, array $column)
    {
        return $type . '.' . str_replace(' ', '.', $column[static::COLUMN_CODE]);
    }

    // {{{ Content helpers

    /**
     * Get service name for this itemslist
     *
     * @return string
     */
    public function getIdentifierClass()
    {
        return strtolower(
            implode('-', $this->getViewClassKeys())
        );
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        $class = parent::getContainerClass()
            . ' items-list-table'
            . ($this->isTableHeaderVisible() ? ' no-thead' : '');

        $class .= ' ' . $this->getIdentifierClass();

        return trim($class);
    }

    /**
     * Get head class
     *
     * @param array $column Column
     *
     * @return string
     */
    protected function getHeadClass(array $column)
    {
        return $column[static::COLUMN_CODE];
    }

    /**
     * Get column cell class
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return string
     */
    protected function getColumnClass(array $column, \XLite\Model\AEntity $entity = null)
    {
        return 'cell '
            . $column[static::COLUMN_CODE]
            . ($this->hasColumnAttention($column, $entity) ? ' attention' : '')
            . ($this->isMainColumn($column) ? ' main' : '')
            . ($this->isEditLinkEnabled($column, $entity) ? ' has-edit-link' : '')
            . (empty($column[static::COLUMN_NO_WRAP]) ? '' : ' no-wrap');
    }

    /**
     * Check - has specified column attention or not
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return boolean
     */
    protected function hasColumnAttention(array $column, \XLite\Model\AEntity $entity = null)
    {
        return false;
    }

    /**
     * Get action cell class
     *
     * @param integer $i        Cell index
     * @param string  $template Template
     *
     * @return string
     */
    protected function getActionCellClass($i, $template)
    {
        return 'action' . (0 < $i ? ' next' : '');
    }

    // }}}

    // {{{ Top / bottom behaviors

    /**
     * Get top actions
     *
     * @return array
     */
    protected function getTopActions()
    {
        $actions = array();

        if (!$this->isStatic()) {
            if (static::CREATE_INLINE_TOP == $this->isCreation()
                && $this->getCreateURL()
                && static::CREATE_INLINE_NONE == $this->isInlineCreation()
            ) {
                $actions[] = 'items_list/model/table/parts/create.twig';

            } elseif (static::CREATE_INLINE_TOP == $this->isInlineCreation()) {
                $actions[] = 'items_list/model/table/parts/create_inline.twig';
            }
        }

        return $actions;
    }

    /**
     * Get bottom actions
     *
     * @return array
     */
    protected function getBottomActions()
    {
        $actions = array();

        if (!$this->isStatic()) {
            if (static::CREATE_INLINE_BOTTOM == $this->isCreation()
                && $this->getCreateURL()
                && static::CREATE_INLINE_NONE == $this->isInlineCreation()
            ) {
                $actions[] = 'items_list/model/table/parts/create.twig';

            } elseif (static::CREATE_INLINE_BOTTOM == $this->isInlineCreation()) {
                $actions[] = 'items_list/model/table/parts/create_inline.twig';
            }
        }

        return $actions;
    }

    // }}}

    // {{{ Line bahaviors

    /**
     * Return sort type
     *
     * @return integer
     */
    protected function getSortType()
    {
        return (static::SORT_TYPE_MOVE === $this->getSortableType() && 1 < $this->getPager()->getPagesCount())
            ? static::SORT_TYPE_INPUT
            : $this->getSortableType();
    }

    /**
     * Get left actions tempaltes
     *
     * @return array
     */
    protected function getLeftActions()
    {
        $list = array();

        if (!$this->isStatic()) {
            if (static::SORT_TYPE_MOVE === $this->getSortType()) {
                $list[] = $this->getMoveActionTemplate();

            } elseif (static::SORT_TYPE_INPUT === $this->getSortType()) {
                $list[] = $this->getPositionActionTemplate();
            }

            if ($this->isSelectable()) {
                $list[] = $this->getSelectorActionTemplate();
            }

            if ($this->isSwitchable()) {
                $list[] = $this->getSwitcherActionTemplate();
            }

            if ($this->isDefault()) {
                $list[] = $this->getDefaultActionTemplate();
            }
        }

        return $list;
    }

    /**
     * Template for position action definition
     *
     * @return string
     */
    protected function getPositionActionTemplate()
    {
        return 'items_list/model/table/parts/position.twig';
    }

    /**
     * Template for move action definition
     *
     * @return string
     */
    protected function getMoveActionTemplate()
    {
        return 'items_list/model/table/parts/move.twig';
    }

    /**
     * Template for selector action definition
     *
     * @return string
     */
    protected function getSelectorActionTemplate()
    {
        return 'items_list/model/table/parts/selector.twig';
    }

    /**
     * Template for switcher action definition
     *
     * @return string
     */
    protected function getSwitcherActionTemplate()
    {
        return 'items_list/model/table/parts/switcher.twig';
    }

    /**
     * Template for default action definition
     *
     * @return string
     */
    protected function getDefaultActionTemplate()
    {
        return 'items_list/model/table/parts/default.twig';
    }

    /**
     * Get title for 'default' action
     *
     * @return string
     */
    protected function getDefaultActionTitle()
    {
        return static::t('Default');
    }

    /**
     * Template for default action definition
     *
     * @return string
     */
    protected function getRemoveActionTemplate()
    {
        return 'items_list/model/table/parts/remove.twig';
    }

    /**
     * Get right actions templates
     *
     * @return array
     */
    protected function getRightActions()
    {
        $list = array();

        if (!$this->isStatic() && $this->isRemoved()) {
            $list[] = $this->getRemoveActionTemplate();
        }

        return $list;
    }

    /**
     * Check - remove entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        return $entity->isPersistent();
    }

    /**
     * Check - switch entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntitySwitch(\XLite\Model\AEntity $entity)
    {
        return (bool)$this->getSwitcherField();
    }

    // }}}

    // {{{ Inherited methods

    /**
     * Check - body tempalte is visible or not
     *
     * @return boolean
     */
    protected function isPageBodyVisible()
    {
        return parent::isPageBodyVisible() || $this->isHeadSearchVisible();
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return 0 < count($this->getTopActions());
    }

    /**
     * isFooterVisible
     *
     * @return boolean
     */
    protected function isFooterVisible()
    {
        return 0 < count($this->getBottomActions());
    }

    /**
     * Return file name for body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return 'model/table.twig';
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return parent::getPageBodyDir() . '/table';
    }

    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        return $this->isAllowEntityRemove($entity) && parent::removeEntity($entity);
    }

    /**
     * isEmptyListTemplateVisible
     *
     * @return boolean
     */
    protected function isEmptyListTemplateVisible()
    {
        return true;
    }

    // }}}

    // {{{ Head sort

    /**
     * Check - specified column is sorted or not
     *
     * @param array $column Column
     *
     * @return boolean
     */
    protected function isColumnSorted(array $column)
    {
        $field = $this->getSortBy();

        return !empty($column[static::COLUMN_SORT]) && $field == $column[static::COLUMN_SORT];
    }

    /**
     * Get next sort direction
     *
     * @param array $column Column
     *
     * @return string
     */
    protected function getSortDirectionNext(array $column)
    {
        if ($this->isColumnSorted($column)) {
            $direction = static::SORT_ORDER_DESC == $this->getSortOrder()
                ? static::SORT_ORDER_ASC
                : static::SORT_ORDER_DESC;

        } else {
            $direction = $this->getSortOrder() ?: static::SORT_ORDER_DESC;
        }

        return $direction;
    }

    /**
     * Get sort link class
     *
     * @param array $column Column
     *
     * @return string
     */
    protected function getSortLinkClass(array $column)
    {
        $classes = 'sort';
        if ($this->isColumnSorted($column)) {
            $classes .= ' current-sort ' . $this->getSortOrder() . '-direction';
        }

        return $classes;
    }

    // }}}

    // {{{ Head search

    /**
     * Check - search-in-head mechanism is available or not
     *
     * @return boolean
     */
    protected function isHeadSearchVisible()
    {
        $found = false;

        foreach ($this->getColumns() as $column) {
            if ($this->isSearchColumn($column)) {
                $found = true;
                break;
            }
        }

        return $found;
    }

    /**
     * Check - specified column has search widget or not
     *
     * @param array $column Column info
     *
     * @return boolean
     */
    protected function isSearchColumn(array $column)
    {
        return !empty($column[static::COLUMN_SEARCH_WIDGET]);
    }


    /**
     * Get search cell class
     *
     * @param array $column Column info
     *
     * @return string
     */
    protected function getSearchCellClass(array $column)
    {
        return 'search-cell ' . $column[static::COLUMN_CODE] . ' '
            . ($this->isSearchColumn($column) ? 'filled' : 'empty');
    }

    // }}}

    /**
     * Check if the column template is used for widget displaying
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isTemplateColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        return !empty($column[static::COLUMN_TEMPLATE]);
    }

    /**
     * Check if the simple class is used for widget displaying
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isClassColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        return !isset($column[static::COLUMN_TEMPLATE]);
    }

    /**
     * Check if the column template is used for widget displaying
     *
     * @param array $column Column
     *
     * @return boolean
     */
    protected function isCreateTemplateColumnVisible(array $column)
    {
        return !isset($column[static::COLUMN_CREATE_CLASS]);
    }

    /**
     * Check if the simple class is used for widget displaying
     *
     * @param array                $column Column
     *
     * @return boolean
     */
    protected function isCreateClassColumnVisible(array $column)
    {
        return isset($column[static::COLUMN_CREATE_CLASS]);
    }

    /**
     * Return true if 'Edit' link should be displayed in column line
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isEditLinkEnabled(array $column, \XLite\Model\AEntity $entity)
    {
        return !empty($column[static::COLUMN_EDIT_LINK]);
    }

    /**
     * Get edit link params string
     * @todo: reorder params
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return string
     */
    protected function getEditLinkAttributes(\XLite\Model\AEntity $entity, array $column)
    {
        return '';
    }

    /**
     * Get label for 'Edit' link
     *
     * @param \XLite\Model\AEntity $entity
     *
     * @return string
     */
    protected function getEditLinkLabel($entity)
    {
        return static::t('Edit');
    }

    /**
     * Check if the column must be a link.
     * It is used if the column field is displayed via
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isLink(array $column, \XLite\Model\AEntity $entity)
    {
        return isset($column[static::COLUMN_LINK]);
    }

    /**
     * Get JS handler class name (used for pagination)
     *
     * @return string
     */
    protected function getJSHandlerClassName()
    {
        return 'TableItemsList';
    }

    /**
     * Prepare field params for
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return array
     */
    protected function preprocessFieldParams(array $column, \XLite\Model\AEntity $entity)
    {
        return $column[static::COLUMN_PARAMS];
    }
}
