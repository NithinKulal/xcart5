<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Category selector
 */
class CategorySelect extends \XLite\View\AView
{
    use CacheableTrait;

    /**
     * Category selector options constants
     */
    const PARAM_ALL_OPTION            = 'allOption';
    const PARAM_NONE_OPTION           = 'noneOption';
    const PARAM_ROOT_OPTION           = 'rootOption';
    const PARAM_FIELD_NAME            = 'fieldName';
    const PARAM_SELECTED_CATEGORY_IDS = 'selectedCategoryIds';
    const PARAM_CURRENT_CATEGORY_ID   = 'currentCategoryId';
    const PARAM_IGNORE_CURRENT_PATH   = 'ignoreCurrentPath';
    const PARAM_IS_MULTIPLE           = 'isMultiple';

    /**
     * Current category ID
     *
     * @var integer
     */
    protected $currentCategoryID;

    /**
     * List of the nodes in current path
     *
     * @var array
     */
    protected $currentPath = array();

    /**
     * Base multiplier of current intendation
     *
     * @var integer
     */
    protected $currentIndent = 0;

    /**
     * Get categories list
     *
     * @return array
     */
    public function getCategories()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Category')->getCategoriesPlainList();
    }

    /**
     * Check - display 'No categories' option or not
     *
     * @return boolean
     */
    public function isDisplayNoCategories()
    {
        return !$this->getParam(self::PARAM_ALL_OPTION) && !$this->getCategories();
    }

    /**
     * Return translated category name
     *
     * @param array $category Category data
     *
     * @return string
     */
    protected function getCategoryName(array $category)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Category')->find($category['category_id'])->getName();
    }

    /**
     * Return category path
     *
     * @param array $category Category data
     *
     * @return string
     */
    protected function getCategoryPath(array $category)
    {
        return implode('/', $this->currentPath);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/select_category.twig';
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
            self::PARAM_ALL_OPTION            => new \XLite\Model\WidgetParam\TypeBool('Display All option', false),
            self::PARAM_NONE_OPTION           => new \XLite\Model\WidgetParam\TypeBool('Display None option', false),
            self::PARAM_ROOT_OPTION           => new \XLite\Model\WidgetParam\TypeBool('Display [Root level] option', false),
            self::PARAM_FIELD_NAME            => new \XLite\Model\WidgetParam\TypeString('Field name', ''),
            self::PARAM_SELECTED_CATEGORY_IDS => new \XLite\Model\WidgetParam\TypeCollection('Selected category ids', array()),
            self::PARAM_CURRENT_CATEGORY_ID   => new \XLite\Model\WidgetParam\TypeInt('Current category id', 0),
            self::PARAM_IGNORE_CURRENT_PATH   => new \XLite\Model\WidgetParam\TypeBool('Ignore current path', false),
            self::PARAM_IS_MULTIPLE           => new \XLite\Model\WidgetParam\TypeBool('Is multiple', false),
        );
    }

    /**
     * Check if specified category is selected
     *
     * @param array $category Category
     *
     * @return boolean
     */
    protected function isCategorySelected(array $category)
    {
        return in_array($category['category_id'], (array) $this->getParam(self::PARAM_SELECTED_CATEGORY_IDS));
    }

    /**
     * getIndentation
     * TODO: review and remove this if this obsolete metho
     *
     * @param array   $category   Category data
     * @param integer $multiplier Level's multiplier
     *
     * @return integer
     */
    protected function getIndentation(array $category, $multiplier)
    {
        $result = $category['depth'];

        if ($result == 0) {
            $this->currentPath = array();
        }

        if ($this->currentCategoryID != $category['category_id']) {

            if ($this->currentIndent >= $result) {
                array_pop($this->currentPath);
            }

            $this->currentPath[] = $this->getCategoryName($category);

            $this->currentCategoryID = $category['category_id'];
            $this->currentIndent     = $result;
        }

        return ($result - 1) * $multiplier + 3;
    }

    /**
     * Return indentation string for displaying category depth level
     *
     * @param array   $category   Category data
     * @param integer $multiplier Level's multiplier
     * @param string  $repeatStr String to be displayed $multiplier times before category name OPTIONAL
     *
     * @return string
     */
    protected function getIndentationString(array $category, $multiplier, $repeatStr = '')
    {
        return str_repeat($repeatStr, $category['depth'] * $multiplier);
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $cacheParams = parent::getCacheParameters();

        foreach($this->getWidgetParams() as $key => $param) {
            $cacheParams[] = serialize($param->value);
        }

        return $cacheParams;
    }
}
