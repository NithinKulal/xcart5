<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Translation;

/**
 * Labels list
 */
class Labels extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget param names
     */
    const PARAM_SUBSTRING  = 'substring';
    const PARAM_CODE       = 'code';

    /**
     * Return URL to search label
     *
     * @param string $substring Substring to search
     * @param string $code      Language code
     *
     * @return string
     */
    public static function getSearchLabelURL($substring, $code = null)
    {
        return \XLite\Core\Converter::buildURL(
            'labels',
            'searchItemsList',
            array(
                'itemsList' => 'XLite\View\ItemsList\Model\Translation\Labels',
                static::PARAM_SUBSTRING => $substring,
                static::PARAM_CODE => $code,
            )
        );
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/labels/controller.js';

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
        return 'labels';
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return '\XLite\View\SearchPanel\Label\Main';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = array(
            'code' => \XLite\Core\Request::getInstance()->code ?: static::getDefaultLanguage(),
        );

        if (\XLite\Core\Request::getInstance()->label_id) {
            $params['label_id'] = \XLite\Core\Request::getInstance()->label_id;
        }

        return array_merge(
            parent::getFormParams(),
            $params
        );
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\LanguageLabel';
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
                static::COLUMN_NAME      => static::t('Label'),
                static::COLUMN_TEMPLATE  => $this->getDir() . '/' . $this->getPageBodyDir() . '/labels/cell.name.twig',
                static::COLUMN_ORDERBY  => 100,
            ),
        );
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return array('labels');
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' labels';
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
    }

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
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return false;
    }

    /**
     * Get default language object
     *
     * @return \XLite\Model\Language
     */
    protected function getDefaultLanguageObject()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Language')->findOneByCode(static::getDefaultLanguage());
    }

    /**
     * Get translation language object
     *
     * @return \XLite\Model\Language
     */
    protected function getTranslationLanguageObject()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Language')->findOneByCode($this->getLanguageCode());
    }

    /**
     * Get translation language code
     *
     * @return string
     */
    protected function getLanguageCode()
    {
        return $this->getParam(static::PARAM_CODE)
            ?: \XLite\Core\Request::getInstance()->code ?: static::getDefaultLanguage();
    }

    /**
     * Get label translation to the default language
     *
     * @param \XLite\Model\LanguageLabel $entity
     *
     * @return string
     */
    protected function getLabelDefaultValue($entity)
    {
        $result = $entity->getTranslation(static::getDefaultLanguage())->label;

        if (!$result && $this->isTranslatedLanguageSelected()) {
            $result = $entity->getName();
        }

        return $result;
    }

    /**
     * Get label translation to the selected language (by code passed as a page argument)
     *
     * @param \XLite\Model\LanguageLabel $entity
     *
     * @return string
     */
    protected function getLabelTranslatedValue($entity)
    {
        return $entity->getTranslation($this->getLanguageCode())->label;
    }

    /**
     * Return true if default language and selected language are different
     *
     * @return boolean
     */
    protected function isTranslatedLanguageSelected()
    {
        return static::getDefaultLanguage() != $this->getLanguageCode();
    }

    // {{{ Search

    /**
     * Return search parameters
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array(
            \XLite\Model\Repo\Order::SEARCH_SUBSTRING  => static::PARAM_SUBSTRING,
        );
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
            static::PARAM_SUBSTRING  => new \XLite\Model\WidgetParam\TypeString('Substring', ''),
            static::PARAM_CODE       => new \XLite\Model\WidgetParam\TypeString('Language code', ''),
        );
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $result->$modelParam = is_string($this->getParam($requestParam))
                ? trim($this->getParam($requestParam))
                : $this->getParam($requestParam);
        }

        $codes = array(static::getDefaultLanguage(), $this->getLanguageCode());
        $result->{\XLite\Model\Repo\LanguageLabel::SEARCH_CODES} = array_unique($codes);

        if (\XLite\Core\Session::getInstance()->added_labels) {
            $result->{\XLite\Model\Repo\LanguageLabel::ORDER_FIRST_BY_IDS} = \XLite\Core\Session::getInstance()->added_labels;
        }

        $result->{\XLite\Model\Repo\LanguageLabel::P_ORDER_BY} = array('l.name', 'ASC');

        return $result;
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')->search($cnd, $countOnly);
    }

    /**
     * Add right actions
     *
     * @return array
     */
    protected function getRightActions()
    {
        $list = parent::getRightActions();

        if ($this->isSeveralLanguagesExists()) {
            array_unshift($list, 'items_list/model/table/labels/action.edit.twig');
        }

        return $list;
    }

    /**
     * Define line class as list of names
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line model OPTIONAL
     *
     * @return array
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity = null)
    {
        $classes = parent::defineLineClass($index, $entity);

        if (
            $entity
            && \XLite\Core\Session::getInstance()->added_labels
            && in_array($entity->getLabelId(), \XLite\Core\Session::getInstance()->added_labels)
        ) {
            $classes[] = 'just-added';
        }

        return $classes;
    }

    /**
     * Get languages list for label translations availability
     *
     * @param \XLite\Model\LanguageLabel $entity
     *
     * @return array
     */
    protected function getLanguageMarks($entity)
    {
        $result = array();

        $languages = \XLite\Core\Database::getRepo('XLite\Model\Language')->findAddedLanguages();

        $translations = $entity->getTranslations();

        foreach ($languages as $l) {
            $code = $l->getCode();
            $found = false;
            foreach ($translations as $t) {
                if ($t->getCode() == $code) {
                    $found = true;
                    break;
                }
            }
            $result[strtoupper($code)] = array(
                'status' => $found ? 'set' : 'unset',
            );

        }

        return $result;
    }

    /**
     * Get remove message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getRemoveMessage($count)
    {
        return \XLite\Core\Translation::lbl('X language labels have been removed', array('count' => $count));
    }

    /**
     * Disable editing of the default label translation
     *
     * @return boolean
     */
    protected function isDefaultLanguageNonEditable()
    {
        return $this->isTranslatedLanguageSelected();
    }

    /**
     * Return true if two or more added languages exists
     *
     * @return boolean
     */
    protected function isSeveralLanguagesExists()
    {
        $languages = \XLite\Core\Database::getRepo('XLite\Model\Language')->findAddedLanguages();

        return 1 < count($languages) || 'en' != $this->getLanguageCode();
    }
}
