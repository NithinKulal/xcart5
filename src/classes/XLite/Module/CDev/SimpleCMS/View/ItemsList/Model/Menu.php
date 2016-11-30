<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View\ItemsList\Model;

/**
 * Menus items list
 */
class Menu extends \XLite\View\ItemsList\Model\Table
{
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
        return 'menus';
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
                'page'  => $this->getPage(),
                'id'    => \XLite\Core\Request::getInstance()->id
            )
        );
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/SimpleCMS/menus/style.css';

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
                static::COLUMN_NAME         => static::t('Item name'),
                static::COLUMN_CLASS        => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS       => array('required' => true),
                static::COLUMN_ORDERBY  => 100,
            ),
            'link' => array(
                static::COLUMN_NAME         => static::t('Link'),
                static::COLUMN_CLASS        => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS       => array('required' => false),
                static::COLUMN_HEAD_HELP    => $this->getColumnLinkHelp(),
                static::COLUMN_ORDERBY  => 200,
            ),
            'visibleFor' => array(
                static::COLUMN_NAME         => static::t('Visible for'),
                static::COLUMN_CLASS        => 'XLite\Module\CDev\SimpleCMS\View\FormField\VisibleFor',
                static::COLUMN_PARAMS       => array('fieldOnly' => true),
                static::COLUMN_ORDERBY  => 300,
            ),
            'submenus' => array(
                static::COLUMN_NAME     => static::t('Submenu'),
                static::COLUMN_TEMPLATE => 'modules/CDev/SimpleCMS/items_list/model/table/menu/parts/info.submenus.twig',
                static::COLUMN_ORDERBY  => 400,
                static::COLUMN_EDIT_LINK => true,
                static::COLUMN_LINK      => 'menus',
            ),
        );
    }

    /**
     * Get Menu
     *
     * @return \XLite\Module\CDev\SimpleCMS\Model\Menu
     */
    protected function getMenu()
    {
        return \XLite\Core\Request::getInstance()->id
            ? \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Menu')
                ->find((int) \XLite\Core\Request::getInstance()->id)
            : \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Menu')->getRootMenu();
    }

    /**
     * Get message for 'link' column header help
     *
     * @return string
     */
    protected function getColumnLinkHelp()
    {
        return static::t('Menu links help text', array('URL' => $this->getShopURL()));
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\CDev\SimpleCMS\Model\Menu';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildURL(
            'menu',
            null,
            array(
                'parent' => $this->getMenu()->getMenuId(),
            )
        );
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        $link = \XLite\Core\Converter::buildURL(
            $column[static::COLUMN_LINK],
            '',
            array(
                'id' => $entity->getMenuId(),
                'page' => $entity->getType(),
            )
        );

        return 'submenus' === $column[static::COLUMN_CODE]
            ? $link
            : parent::buildEntityURL($entity, $column);
    }

    /**
     * Create entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();

        $entity->setType($this->getPage());

        $parent = null;
        if (\XLite\Core\Request::getInstance()->id) {
            $parent = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Menu')
                ->find((int) \XLite\Core\Request::getInstance()->id);
        }

        if (!$parent) {
            $parent = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Menu')->getRootMenu();
        }

        $entity->setParent($parent);

        return $entity;
    }

    // {{{ Search

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

        $result->type = $this->getPage();

        $result->{\XLite\Module\CDev\SimpleCMS\Model\Repo\Menu::SEARCH_PARENT} = \XLite\Core\Request::getInstance()->id
            ? (int) \XLite\Core\Request::getInstance()->id
            : \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Menu')->getRootMenuId();

        return $result;
    }

    // }}}
    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New item';
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
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
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
        return 'submenus' === $column[static::COLUMN_CODE]
            ? parent::isEditLinkEnabled($column, $entity)
                && !$entity->getSubmenusCount()
            : parent::isEditLinkEnabled($column, $entity);
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
        return static::t('Add');
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' menus';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\CDev\SimpleCMS\View\StickyPanel\ItemsList\Menu';
    }

    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return array();
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['page'] = $this->getPage();

        return $this->commonParams;
    }

    /**
     * Return true if param value may contain anything
     *
     * @param string $name Param name
     *
     * @return boolean
     */
    protected function isParamTrusted($name)
    {
        return $name === 'link';
    }

    /**
     * Return name of the session cell identifier
     *
     * @return string
     */
    public function getSessionCell()
    {
        return parent::getSessionCell() . $this->getPage();
    }

    // }}}
}
