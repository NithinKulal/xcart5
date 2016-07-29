<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\ItemsList\Model;
use XLite\Core\Templating\CacheManagerInterface;

/**
 * Theme tweaker templates items list
 */
class Template extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/ThemeTweaker/theme_tweaker_templates/style.css';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/ThemeTweaker/theme_tweaker_templates/controller.js';

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
            'template' => array(
                static::COLUMN_NAME    => static::t('Template'),
                static::COLUMN_MAIN    => true,
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_LINK    => 'theme_tweaker_template',
                static::COLUMN_ORDERBY => 100,
            ),
            'date' => array(
                static::COLUMN_NAME     => static::t('Date'),
                static::COLUMN_TEMPLATE => 'modules/XC/ThemeTweaker/theme_tweaker_templates/parts/cell.date.twig',
                static::COLUMN_NO_WRAP  => true,
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
        return 'XLite\Module\XC\ThemeTweaker\Model\Template';
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
        return parent::getContainerClass() . ' theme_tweaker_templates';
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
     * Returns full path
     *
     * @param string $shortPath Short path
     * @param string $skin      Skin OPTIONAL
     *
     * @return string
     */
    protected function getFullPathByShortPath($shortPath, $skin = 'theme_tweaker/customer')
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
     * Returns a (cached) templating engine instance
     *
     * @return CacheManagerInterface
     */
    protected function getTemplateCacheManager()
    {
        return $this->getContainer()->get('template_cache_manager');
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
        $pathSkin = 'theme_tweaker/customer';
        $localPath = $entity->getTemplate();

        $shortPath = substr($localPath, strpos($localPath, LC_DS, strlen($pathSkin)));
        $fullPath = $this->getFullPathByShortPath($shortPath);

        \Includes\Utils\FileManager::deleteFile($fullPath);

        $this->getTemplateCacheManager()->invalidate($fullPath);

        parent::removeEntity($entity);

        return true;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Zone::P_ORDER_BY} = array('t.date', 'DESC');

        return $result;
    }

    /**
     * isEmptyListTemplateVisible
     *
     * @return boolean
     */
    protected function isEmptyListTemplateVisible()
    {
        return false;
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\ThemeTweaker\View\StickyPanel\TemplatesForm';
    }
}
