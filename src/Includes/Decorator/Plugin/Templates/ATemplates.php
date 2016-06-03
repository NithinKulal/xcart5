<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Templates;

/**
 * ATemplates
 *
 * @package XLite
 */
abstract class ATemplates extends \Includes\Decorator\Plugin\APlugin
{
    /**
     * Predefined tag names
     */
    const TAG_LIST_CHILD           = 'listchild';
    const TAG_INHERITED_LIST_CHILD = 'inheritedlistchild';
    const TAG_ADD_LIST_CHILD       = 'addlistchild';
    const TAG_CLEAR_LIST_CHILDREN  = 'clearlistchildren';

    /**
     * List of .twig files
     *
     * @var array
     */
    protected static $annotatedTemplates;

    /**
     * List of .twig files with @InheritedListChild tag
     *
     * @var array
     */
    protected static $inheritedTemplates;

    /**
     * List of zones
     *
     * @var array
     */
    protected static $zones = array(
        'console' => \XLite\Model\ViewList::INTERFACE_CONSOLE,
        'admin'   => \XLite\Model\ViewList::INTERFACE_ADMIN,
        'mail'    => \XLite\Model\ViewList::INTERFACE_MAIL,
        'pdf'     => \XLite\Model\ViewList::INTERFACE_PDF,
    );

    /**
     * Return templates list
     *
     * @return array
     */
    protected function getAnnotatedTemplates()
    {
        if (!isset(static::$annotatedTemplates)) {
            static::$annotatedTemplates = array();
            static::$inheritedTemplates = array();

            foreach ($this->getTemplateFileIterator()->getIterator() as $path => $data) {

                $data = \Includes\Decorator\Utils\Operator::getTags(
                    \Includes\Utils\FileManager::read($path, true),
                    array(
                        static::TAG_LIST_CHILD,
                        static::TAG_INHERITED_LIST_CHILD,
                        static::TAG_ADD_LIST_CHILD,
                        static::TAG_CLEAR_LIST_CHILDREN
                    )
                );

                if (
                    isset($data[static::TAG_LIST_CHILD])
                    || isset($data[static::TAG_ADD_LIST_CHILD])
                    || isset($data[static::TAG_CLEAR_LIST_CHILDREN])
                ) {
                    $tmp = $data;
                    if (isset($tmp[static::TAG_INHERITED_LIST_CHILD])) {
                        unset($tmp[static::TAG_INHERITED_LIST_CHILD]);
                    }
                    
                    $this->addTags($tmp, $path);
                }

                if (isset($data[static::TAG_INHERITED_LIST_CHILD])) {
                    static::$inheritedTemplates[] = $path;
                }
            }
        }

        return static::$annotatedTemplates;
    }

    /**
     * Get iterator for template files
     *
     * @return \Includes\Utils\FileFilter
     */
    protected function getTemplateFileIterator()
    {
        return new \Includes\Utils\FileFilter(
            LC_DIR_SKINS,
            \Includes\Utils\ModulesManager::getPathPatternForTemplates()
        );
    }

    /**
     * Parse template and add tags to the list
     *
     * @param array  $data Tags data
     * @param string $path Template file path
     *
     * @return array
     */
    protected function addTags(array $data, $path)
    {
        $base = \Includes\Utils\FileManager::getRelativePath($path, LC_DIR_SKINS);
        $skin = \Includes\Utils\ArrayManager::getIndex(explode(LC_DS, $base), 0, true);

        static::$annotatedTemplates[] = array(
            'tpl'  => $base,
            'zone' => array_search($skin, static::$zones) ?: \XLite\Model\ViewList::INTERFACE_CUSTOMER,
            'path' => $path,
            'tags' => $data,
        );
    }
}
