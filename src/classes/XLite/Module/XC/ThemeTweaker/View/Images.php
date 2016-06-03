<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

/**
 * Images widget
 */
class Images extends \XLite\View\AView
{
    protected $images;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('images'));
    }

   /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'items_list/model/table/style.css';
        $list[] = 'items_list/model/style.css';
        $list[] = 'modules/XC/ThemeTweaker/images/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/images/body.twig';
    }


    /**
     * Get iterator for template files
     *
     * @return \Includes\Utils\FileFilter
     */
    protected function getImagesIterator()
    {
        return new \Includes\Utils\FileFilter(
            $this->getImagesDir()
        );
    }

    /**
     * Get images 
     *
     * @return array
     */
    protected function getImages()
    {
        if (!isset($this->images)) {
            $this->images = array();
            try {
                foreach ($this->getImagesIterator()->getIterator() as $file) {
                    if ($file->isFile()) {
                        $this->images[] = \Includes\Utils\FileManager::getRelativePath($file->getPathname(), $this->getImagesDir());
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return $this->images;
    }

    /**
     * Get image dir
     *
     * @param string $image Image
     *
     * @return string
     */
    protected function getImageUrl($image)
    {
        return \XLite\Core\Layout::getInstance()->getResourceWebPath(
            'theme/images/' . $image,
            \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
            'custom'
        );
    }

    /**
     * Get images dir
     *
     * @return string
     */
    protected function getImagesDir()
    {
        return \XLite\Module\XC\ThemeTweaker\Main::getThemeDir() . 'images' . LC_DS;
    }
}
