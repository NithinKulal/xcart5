<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

/**
 * Password (visible variant)
 */
class PasswordVisible extends \XLite\View\FormField\Input\Secure
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/js/password_visible.js';

        return $list;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'password_visible.twig';
    }

    /**
     * Prepare attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function prepareAttributes(array $attrs)
    {
        $attrs = parent::prepareAttributes($attrs);

        $attrs['class'] = (empty($attrs['class']) ? '' : $attrs['class'] . ' ')
            . 'password-visible';

        return $attrs;
    }

    /**
     * Return true if value is trusted (purification must be ignored)
     *
     * @return boolean
     */
    public function isTrusted()
    {
        return true;
    }
}
