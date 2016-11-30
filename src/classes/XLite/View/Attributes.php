<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Attributes page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Attributes extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('attributes'));
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'attribute/style.css';
        $list[] = 'attributes/style.css';
        $list[] = 'form_field/inline/style.css';
        $list[] = 'form_field/inline/input/text/position/move.css';
        $list[] = 'form_field/inline/input/text/position.css';
        $list[] = 'form_field/form_field.css';
        $list[] = 'form_field/input/text/position.css';
        $list[] = 'form_field/input/checkbox/switcher.css';
        $list[] = 'items_list/items_list.css';
        $list[] = 'items_list/model/style.css';
        $list[] = 'items_list/model/table/style.css';
        $list[] = 'form_field/inline/input/text.css';

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'form_field/inline/controller.js';
        $list[] = 'form_field/inline/input/text/position/move.js';
        $list[] = 'form_field/js/text.js';
        $list[] = 'form_field/input/text/integer.js';
        $list[] = 'form_field/input/checkbox/switcher.js';
        $list[] = 'button/js/remove.js';
        $list[] = 'items_list/items_list.js';
        $list[] = 'items_list/model/table/controller.js';
        $list[] = 'attributes/script.js';
        $list[] = 'form_field/inline/input/text.js';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'js/jquery.textarea-expander.js';
        $list[static::RESOURCE_JS][] = 'js/tooltip.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'attributes/body.twig';
    }

    /**
     * Check - search box is visible or not
     *
     * @return boolean
     */
    protected function isSearchVisible()
    {
        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Attribute')->count();
    }

    /**
     * Check - list box is visible or not
     *
     * @return boolean
     */
    protected function isListVisible()
    {
        return $this->getAttributesCount()
            || count($this->getAttributeGroups());
    }

    /**
     * Return true if top buttons should be visible
     *
     * @return boolean
     */
    protected function isButtonsBlockVisible()
    {
        return true;
    }
}
