<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Section-based dialog
 */
abstract class SectionDialog extends \XLite\View\SimpleDialog
{
    /**
     * Define sections list
     *
     * @return array
     */
    abstract protected function defineSections();

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        $mode = strval(\XLite\Core\Request::getInstance()->mode);
        $sections = $this->defineSections();

        return (!empty($sections[$mode]) && !empty($sections[$mode]['head']))
            ? $sections[$mode]['head']
            : parent::getHead();
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        $mode = strval(\XLite\Core\Request::getInstance()->mode);
        $sections = $this->defineSections();

        return isset($sections[$mode]) ? $sections[$mode]['body'] : null;
    }
}
