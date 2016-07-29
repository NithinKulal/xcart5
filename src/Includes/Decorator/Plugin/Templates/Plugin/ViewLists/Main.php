<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Templates\Plugin\ViewLists;

use Includes\Annotations\Parser\AnnotationParserFactory;
use Includes\ClassPathResolver;
use Includes\Decorator\Utils\Operator;
use Includes\Reflection\StaticReflectorFactory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * Main
 *
 */
class Main extends \Includes\Decorator\Plugin\Templates\Plugin\APlugin
{
    /**
     * Parameters for the tags
     */
    const PARAM_TAG_LIST_CHILD_CLASS      = 'class';
    const PARAM_TAG_LIST_CHILD_LIST       = 'list';
    const PARAM_TAG_LIST_CHILD_WEIGHT     = 'weight';
    const PARAM_TAG_LIST_CHILD_ZONE       = 'zone';
    const PARAM_TAG_LIST_CHILD_FIRST      = 'first';
    const PARAM_TAG_LIST_CHILD_LAST       = 'last';
    const PARAM_TAG_LIST_CHILD_CONTROLLER = 'controller';

    /**
     * Temporary index to use in templates hash
     */
    const PREPARED_SKIN_NAME = '____PREPARED____';

    /**
     * List of PHP classes with the "ListChild" tags
     *
     * @var array
     */
    protected $annotatedPHPCLasses;

    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        // Truncate old
        if (!\Includes\Decorator\Utils\CacheManager::isCapsular()) {
            $this->clearAll();
        }

        // Create new
        $this->createLists();
    }

    /**
     * Remove existing lists from database
     *
     * @return void
     */
    protected function clearAll()
    {
        \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->clearAll();
    }

    /**
     * Create lists
     *
     * @return void
     */
    protected function createLists()
    {
        \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->insertInBatch($this->getAllListChildTags());
    }

    /**
     * Return all defined "ListChild" tags
     *
     * @return array
     */
    protected function getAllListChildTags()
    {
        return array_merge($this->getListChildTagsFromPHP(), $this->getListChildTagsFromTemplates());
    }

    /**
     * Return list of PHP classes with the "ListChild" tag
     *
     * @return array
     */
    protected function getAnnotatedPHPCLasses()
    {
        if (!isset($this->annotatedPHPCLasses)) {
            $this->annotatedPHPCLasses = array();

            $classPathResolver = new ClassPathResolver(Operator::getClassesDir());
            $reflectorFactory  = new StaticReflectorFactory($classPathResolver);

            foreach ($this->getViewClasses() as $pathname) {
                $reflector   = $reflectorFactory->reflectSource($pathname);
                $annotations = $reflector->getClassAnnotationsOfType('Includes\Annotations\ListChild');

                foreach ($annotations as $annotation) {
                    $newListChild = [
                        'child'  => $classPathResolver->getClass($pathname),
                        'list'   => $annotation->list,
                        'weight' => $annotation->weight,
                    ];

                    if ($annotation->zone) {
                        $newListChild['zone'] = $annotation->zone;
                    }

                    /** @var \Includes\Annotations\ListChild $annotation */
                    $this->annotatedPHPCLasses[] = $newListChild;
                }
            }
        }

        return $this->annotatedPHPCLasses;
    }

    /**
     * Returns an iterator for all core's and modules' View files
     *
     * @return array
     */
    protected function getViewClasses()
    {
        $classes = Operator::getClassesDir();

        $viewDirs = array_merge(
            [$classes . 'XLite/View'],
            glob($classes . 'XLite/Module/*/*/View')
        );

        $viewFiles = [];

        foreach ($viewDirs as $dir) {
            $iterator   = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
            $filesFiles = new RegexIterator($iterator, '/^.+\.php$/', RegexIterator::GET_MATCH);

            foreach ($filesFiles as $files) {
                foreach ($files as $file) {
                    $viewFiles[] = $file;
                }
            }
        }

        return $viewFiles;
    }

    /**
     * Return all "ListChild" tags defined in PHP classes
     *
     * @return array
     */
    protected function getListChildTagsFromPHP()
    {
        return $this->getAllListChildTagAttributes($this->getAnnotatedPHPCLasses());
    }

    /**
     * Return all "ListChild" tags defined in templates
     *
     * @return array
     */
    protected function getListChildTagsFromTemplates()
    {
        return $this->getAllListChildTagAttributes($this->prepareListChildTemplates($this->getAnnotatedTemplates()));
    }

    /**
     * Prepare list childs templates-based
     *
     * @param array $list List
     *
     * @return array
     */
    protected function prepareListChildTemplates(array $list)
    {
        $result = array();

        \XLite::getInstance()->initModules();

        $skins = array();
        $hasSubstSkins = false;

        foreach (\XLite\Core\Layout::getInstance()->getSkinsAll() as $interface => $path) {
            $skins[$interface] = \XLite\Core\Layout::getInstance()->getSkins($interface);

            if (!$hasSubstSkins) {
                $hasSubstSkins = 1 < count($skins[$interface]);
            }
        }

        foreach ($list as $i => $cell) {
            foreach ($skins as $interface => $paths) {
                foreach ($paths as $path) {
                    if (0 === strpos($cell['tpl'], $path . LC_DS)) {
                        $length = strlen($path) + 1;
                        $list[$i]['tpl'] = substr($cell['tpl'], $length);
                        $list[$i]['zone'] = $interface;
                    }
                }
            }

            if (!isset($list[$i]['zone'])) {
                unset($list[$i]);
            }
        }

        if ($hasSubstSkins) {
            $patterns = $hash = array();

            foreach ($skins as $interface => $data) {
                $patterns[$interface] = array();

                foreach ($data as $skin) {
                    $patterns[$interface][] = preg_quote($skin, '/');
                }

                $patterns[$interface] = '/^(' . implode('|', $patterns[$interface]) . ')' . preg_quote(LC_DS, '/') . '(.+)$/US';
            }

            foreach ($list as $index => $item) {
                $path = \Includes\Utils\FileManager::getRelativePath($item['path'], LC_DIR_SKINS);

                if (preg_match($patterns[$item['zone']], $path, $matches)) {
                    $hash[$item['zone']][$item['tpl']][$matches[1]] = $index;
                    $list[$index]['tpl'] = $matches[2];
                }
            }

            foreach ($hash as $interface => $tpls) {
                foreach ($tpls as $path => $indexes) {
                    $idx = null;
                    $tags = array();
                    foreach (array_reverse($skins[$interface]) as $skin) {
                        if (isset($indexes[$skin])) {
                            $idx = $indexes[$skin];
                            $tags[] = $list[$indexes[$skin]]['tags'];
                        }
                    }

                    foreach ($this->processTagsQuery($tags) as $tag) {
                        $tmp = $list[$idx];
                        unset($tmp['tags'], $tmp['path']);
                        $result[] = $tmp + $tag;
                    }
                }
            }

            // Convert template short path to UNIX-style
            if (DIRECTORY_SEPARATOR != '/') {
                foreach ($result as $i => $v) {
                    $result[$i]['tpl'] = str_replace(DIRECTORY_SEPARATOR, '/', $v['tpl']);
                }
            }

        } else {

            foreach ($list as $cell) {
                foreach ($this->processTagsQuery(array($cell['tags'])) as $tag) {
                    unset($cell['tags'], $cell['path']);
                    $result[] = $cell + $tag;
                }
            }

        }

        return $result;
    }

    /**
     * Process tags query
     *
     * @param array $tags Tags query
     *
     * @return array
     */
    protected function processTagsQuery(array $tags)
    {
        $result = array();

        foreach ($tags as $step) {
            if (isset($step[static::TAG_CLEAR_LIST_CHILDREN])) {
                $result = array();
            }

            if (isset($step[static::TAG_LIST_CHILD])) {
                $result = $step[static::TAG_LIST_CHILD];
            }

            if (isset($step[static::TAG_ADD_LIST_CHILD])) {
                $result = array_merge($result, $step[static::TAG_ADD_LIST_CHILD]);
            }
        }

        return $result;
    }

    /**
     * Return all defined "ListChild" tag attributes
     *
     * @param array $nodes List of nodes
     *
     * @return array
     */
    protected function getAllListChildTagAttributes(array $nodes)
    {
        return array_map(array($this, 'prepareListChildTagData'), $nodes);
    }

    /**
     * Prepare attributes of the "ListChild" tag
     *
     * @param array $data Tag attributes
     *
     * @return array
     */
    protected function prepareListChildTagData(array $data)
    {
        // Check the weight-related attributes
        $this->prepareWeightAttrs($data);

        // Check for preprocessors
        $this->preparePreprocessors($data);

        return $data;
    }

    /**
     * Check the weight-related attributes
     *
     * @param array &$data Data to prepare
     *
     * @return void
     */
    protected function prepareWeightAttrs(array &$data)
    {
        // The "weight" attribute has a high priority
        if (!isset($data[static::PARAM_TAG_LIST_CHILD_WEIGHT])) {

            // "First" and "last" - the reserved keywords for the "weight" attribute values
            foreach ($this->getReservedWeightValues() as $origKey => $modelKey) {

                if (isset($data[$origKey])) {
                    $data[static::PARAM_TAG_LIST_CHILD_WEIGHT] = $modelKey;
                }
            }
        } else {

            $data[static::PARAM_TAG_LIST_CHILD_WEIGHT] = intval($data[static::PARAM_TAG_LIST_CHILD_WEIGHT]);
        }

        // Set default value
        if (!isset($data[static::PARAM_TAG_LIST_CHILD_WEIGHT])) {
            $data[static::PARAM_TAG_LIST_CHILD_WEIGHT] = \XLite\Model\ViewList::POSITION_LAST;
        }
    }

    /**
     * Check for so called list "preprocessors"
     *
     * @param array &$data Data to use
     *
     * @return void
     */
    protected function preparePreprocessors(array &$data)
    {
        if (isset($data[static::PARAM_TAG_LIST_CHILD_CONTROLLER])) {
            // ...
        }
    }

    /**
     * There are some reserved words for the "weight" param of the "ListChild" tag
     *
     * @return void
     */
    protected function getReservedWeightValues()
    {
        return array(
            static::PARAM_TAG_LIST_CHILD_FIRST => \XLite\Model\ViewList::POSITION_FIRST,
            static::PARAM_TAG_LIST_CHILD_LAST  => \XLite\Model\ViewList::POSITION_LAST,
        );
    }
}
