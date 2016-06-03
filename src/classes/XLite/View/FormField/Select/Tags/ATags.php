<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Tags;

/**
 * Multiple select based on checkboxes list
 */
abstract class ATags extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/tags.js';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = 'js/chosen.jquery.js';

        $list[static::RESOURCE_CSS][] = 'css/chosen/chosen.css';

        return $list;
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
            . 'tags';

        return $attrs;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        return parent::setCommonAttributes($attrs)
            + array(
                'data-placeholder'   => static::t('Enter keywords'),
                'data-selected-text' => static::t('# selected'),
            );
    }

    /**
     * Set the form field as "form control" (some major styling will be applied)
     *
     * @return boolean
     */
    protected function isFormControl()
    {
        return true;
    }
}
