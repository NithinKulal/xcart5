<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LanguagesModify;

/**
 * Languages page widget
 */
class Languages extends \XLite\View\AView
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/functions.js';

        return $list;
    }

    /**
     * Get name of the working directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'languages';
    }

    /**
     * Return widget template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/languages/body.twig';
    }

    /**
     * Return true is language importing process is active
     *
     * @return boolean
     */
    protected function isImportActive()
    {
        return (boolean)\XLite\Core\Session::getInstance()->language_import_file;
    }
}
