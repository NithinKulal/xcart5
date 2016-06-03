<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Utils;

/**
 * Operator
 *
 */
abstract class Operator extends \Includes\Decorator\Utils\AUtils
{
    protected static $classesDirLength = 0;
    
    /**
     * Tags to ignore
     *
     * @var array
     */
    protected static $ignoredTags = array('see', 'since');

    /**
     * Cached modules 
     * 
     * @var   array
     */
    protected static $cachedModules;

    // {{ Classes tree

    /**
     * Get iterator for class files
     *
     * @return \Includes\Utils\FileFilter
     */
    public static function getClassFileIterator()
    {
        return new \Includes\Utils\FileFilter(
            static::getClassesDir(),
            \Includes\Utils\ModulesManager::getPathPatternForPHP()
        );
    }

    // }}}

    // {{{ Modules graph

    /**
     * Check all module dependencies and create the graph
     *
     * @return \Includes\Decorator\DataStructure\Graph\Modules
     */
    public static function createModulesGraph()
    {
        // Tree is not a separate data structure - it's only the root node
        $root = new \Includes\Decorator\DataStructure\Graph\Modules();

        // It's the (<module_name, descriptor>) list
        foreach (($index = static::getModulesGraphIndex()) as $node) {

            // Two possibilities:
            // 1. Module have dependencies. Add module as a child to all its parents
            // 2. Module have no dependencies. Add it as a child to the root node
            if ($dependencies = $node->getDependencies()) {

                // It's the (<module_name>) list
                foreach ($dependencies as $module) {

                    // Module from the dependencies may be disbaled,
                    // or included into the mutual modules list
                    // of some other module(s)
                    if (isset($index[$module])) {

                        // Case 1 (with dependencies)
                        $index[$module]->addChild($node);
                    }
                }

            } else {

                // Case 2 (without dependencies)
                $root->addChild($node);
            }
        }

        // Check modules graph integrity
        $root->checkIntegrity();

        return $root;
    }

    /**
     * Get all active modules and return plain array with the module descriptors
     *
     * @return array
     */
    protected static function getModulesGraphIndex()
    {
        $index = array();

        // Fetch all active modules from database.
        // Dependencies are checked and corrected by the ModulesManager
        foreach (\Includes\Utils\ModulesManager::getActiveModules() as $module => $tmp) {

            // Unconditionally add module to the index (since its dependencies are already checked)
            $index[$module] = new \Includes\Decorator\DataStructure\Graph\Modules($module);
        }

        return $index;
    }

    // }}}

    // {{{ Tags parsing

    /**
     * Parse dockblock to get tags
     *
     * @param string $content String to parse
     * @param array  $tags    Tags to search OPTIONAL
     *
     * @return array
     */
    public static function getTags($content, array $tags = array())
    {
        $result = array();

        if (preg_match_all(static::getTagPattern($tags), $content, $matches)) {
            $tags = static::parseTags($matches);

            if (!empty($tags)) {
                $result += static::parseTags($matches);
            }
        }

        return $result;
    }

    /**
     * Return pattern to parse source for tags
     *
     * @param array $tags List of tags to search
     *
     * @return string
     */
    public static function getTagPattern(array $tags)
    {
        return '/@\s*(' . (empty($tags) ? '\w+' : implode('|', $tags)) . ')(?=\s*)([^@\n]*)?/Smi';
    }

    /**
     * Parse dockblock to get tags
     *
     * @param array $matches Data from preg_match_all()
     *
     * @return array
     */
    protected static function parseTags(array $matches)
    {
        $result = array(array(), array());

        // Sanitize data
        array_walk($matches[2], function (&$value) { $value = trim(trim($value), ')('); });

        // There are so called "multiple" tags
        foreach (array_unique($matches[1]) as $tag) {

            // Ignore some time to save memory and time
            if (in_array($tag, static::$ignoredTags)) continue;

            // Check if tag is defined only once
            if (1 < count($keys = array_keys($matches[1], $tag))) {
                $list = array();

                // Convert such tag values into the single array
                foreach ($keys as $key) {

                    // Parse list of tag attributes and their values
                    $list[] = static::parseTagValue($matches[2][$key]);
                }

                // Add tag name and its values to the end of tags list.
                // All existing entries for this tag was cleared by the "unset()"
                $result[0][] = $tag;
                $result[1][] = $list;

            // If the value was parsed (the corresponded tokens were found), change its type to the "array"
            } elseif ($matches[2][$key = array_shift($keys)] !== ($value = static::parseTagValue($matches[2][$key]))) {

                $result[0][] = $tag;
                $result[1][] = array($value ?: $matches[2][$key]);
            }
        }

        // Create an associative array of tag names and their values
        return !empty($result[0]) && !empty($result[1])
            ? array_combine(array_map('strtolower', $result[0]), $result[1])
            : array();
    }

    /**
     * Parse value of a phpDocumenter tag
     *
     * @param string $value Value to parse
     *
     * @return array
     */
    protected static function parseTagValue($value)
    {
        return \Includes\Utils\Converter::parseQuery($value, '=', ',', '"\'');
    }

    // }}}
}
