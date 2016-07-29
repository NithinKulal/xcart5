<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\ItemsList\Model;

/**
 * Theme tweaker templates items list
 */
class FlexyToTwig extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'flexyTemplate' => array(
                static::COLUMN_NAME    => static::t('Flexy-template'),
                static::COLUMN_TEMPLATE => 'modules/XC/ThemeTweaker/flexy_to_twig/parts/cell.flexyTemplate.twig',
                static::COLUMN_MAIN    => false,
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 100,
            ),
            'twigTemplate' => array(
                static::COLUMN_NAME    => static::t('Twig-template path'),
                static::COLUMN_TEMPLATE => 'modules/XC/ThemeTweaker/flexy_to_twig/parts/cell.twigTemplate.twig',
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 200,
            ),
            'converted' => array(
                static::COLUMN_NAME     => static::t('Template is converted'),
                static::COLUMN_TEMPLATE => 'modules/XC/ThemeTweaker/flexy_to_twig/parts/cell.converted.twig',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_ORDERBY  => 300,
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
        return 'XLite\Module\XC\ThemeTweaker\Model\Template';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' flexy-to-twig';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return '\XLite\View\Pager\Infinity';
    }

    /**
     * Return templates list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $result = \XLite\Module\XC\ThemeTweaker\Core\Flexy::getInstance()->getTemplateObjects();

        if ($countOnly) {
            $result = count($result);
        }

        return $result;
    }

    /**
     * Returns full path
     *
     * @param string $shortPath Short path
     * @param string $skin      Skin OPTIONAL
     *
     * @return string
     */
    protected function getFullPathByShortPath($shortPath, $skin = 'theme_tweaker/default')
    {
        $result = '';

        /** @var \XLite\Core\Layout $layout */
        $layout = \XLite\Core\Layout::getInstance();

        foreach ($layout->getSkinPaths(\XLite::CUSTOMER_INTERFACE) as $path) {
            if ($path['name'] == $skin) {
                $result = $path['fs'] . LC_DS . $shortPath;

                break;
            }
        }

        return $result;
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

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\ThemeTweaker\View\StickyPanel\FlexyToTwigForm';
    }

    /**
     * Get empty list template
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return 'modules/XC/ThemeTweaker/flexy_to_twig/empty.twig';
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
        $result = parent::defineLineClass($index, $entity);

        if (!$entity->isOrigExists()) {
            $result[] = 'orphan';
        }

        return $result;
    }
}
