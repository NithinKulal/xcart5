<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\CheckboxList;

/**
 * Multiple select based on checkboxes list
 */
abstract class ACheckboxList extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/checkbox_list.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/checkbox_list.css';

        return $list;
    }

    /**
     * Set value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        if (
            is_array($value)
            && 1 == sizeof($value)
            && isset($value[0])
            && '' === $value[0]
        ) {
            $value = array();
        }

        parent::setValue($value);
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = 'js/jquery.multiselect.min.js';
        $list[static::RESOURCE_JS][] = 'js/jquery.multiselect.filter.min.js';

        $list[static::RESOURCE_CSS][] = 'css/jquery.multiselect.css';
        $list[static::RESOURCE_CSS][] = 'css/jquery.multiselect.filter.css';

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
            . 'checkbox-list';

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
                'data-placeholder'              => static::t('Select options'),
                'data-selected-text'            => static::t('# selected'),
                'data-selected-list-threshold'  => $this->getSelectedListThreshold(),
            );
    }

    /**
     * Get selected list threshold
     *
     * @return  integer
     */
    protected function getSelectedListThreshold()
    {
        return 2;
    }

    /**
     * Set the form field as "form control" (some major styling will be applied)
     *
     * @return boolean
     */
    protected function isFormControl()
    {
        return false;
    }
}
