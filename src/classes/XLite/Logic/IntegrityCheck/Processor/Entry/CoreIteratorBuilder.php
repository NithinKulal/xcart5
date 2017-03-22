<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\IntegrityCheck\Processor\Entry;


class CoreIteratorBuilder
{
    /**
     * Exclude pattern
     *
     * @var string
     */
    protected $excludePattern;

    /**
     * Include pattern
     *
     * @var string
     */
    protected $includePattern;

    /**
     * @var string
     */
    protected $mandatoryPattern;

    /**
     * Return iterator to walk through directories
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        $result = new \Includes\Utils\FileFilter(LC_DIR_ROOT);
        $result = $result->getIterator();

        $this->excludePattern = $this->preparePatternsList(
            $this->getExcludedList()
        );
        $this->includePattern = $this->preparePatternsList(
            $this->getIncludedList()
        );

        $this->mandatoryPattern = $this->preparePatternsList(
            $this->getMandatoryList()
        );

        $result->registerCallback(array($this, 'filterCoreFiles'));

        return $result;
    }

    /**
     * @return array
     */
    protected function getExcludedList()
    {
        return [
            'list' => [
                'var',
                'files',
                'images',
                'sql',
                'etc' . LC_DS . 'config.local.php',
                'etc' . LC_DS . 'config.personal.php',
                'etc' . LC_DS . 'config.php',
                'lib' . LC_DS . 'dompdf' . LC_DS . 'lib' . LC_DS . 'fonts',
                'classes' . LC_DS . 'XLite' . LC_DS . 'Module',
                'vendor',
                'public',
                'composer.json',
                'composer.lock',
                'LICENSE.txt',
                'LICENSE.txt.ru',
                'CLOUDSEARCHTERMS.txt',
            ],
            'raw' => [
                "skins\/.*\/modules",
                ".*\/.log",
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getIncludedList()
    {
        return [
            'list'   => [
                'classes' . LC_DS . 'XLite' . LC_DS . 'Module' . LC_DS . 'AModule',
                'classes' . LC_DS . 'XLite' . LC_DS . 'Module' . LC_DS . 'AModuleSkin.php',
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getMandatoryList()
    {
        $list = array_map(
            function ($interface) {
                return 'skins' . LC_DS . $interface;
            },
            \XLite\Core\Layout::getInstance()->getSkinsAll()
        );

        return ['list' => $list];
    }

    /**
     * Prepare patterns list
     *
     * @return string
     */
    protected function preparePatternsList($list)
    {
        $list = array_merge(
            ['list' => [], 'raw' => []],
            $list
        );

        $toImplode = $list['raw'];

        foreach ($list['list'] as $pattern) {
            $toImplode[] = preg_quote($pattern, '/');
        }

        return  '/^(?:' . implode('|', $toImplode) . ')/Ss';
    }

    /**
     * Callback to filter files
     *
     * @param \Includes\Utils\FileFilter\FilterIterator $iterator Directory iterator
     *
     * @return boolean
     */
    public function filterCoreFiles(\Includes\Utils\FileFilter\FilterIterator $iterator)
    {
        // Relative path in LC root directory
        $path = \Includes\Utils\FileManager::getRelativePath($iterator->getPathname(), LC_DIR_ROOT);

        return (!preg_match($this->excludePattern, $path)
                || preg_match($this->includePattern, $path)
            ) && (strpos($path, 'skins') === false || preg_match($this->mandatoryPattern, $path));
    }

    // }}}
}
