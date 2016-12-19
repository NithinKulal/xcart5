<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\Model;

use XLite\Core\Database;

/**
 * General settings widget extension
 */
class Settings extends \XLite\View\Model\Settings implements \XLite\Base\IDecorator
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/CloudSearch/general_settings.css';

        return $list;
    }

    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        if ('default_search_sort_order' == $option->getName() && $cell) {
            $module = Database::getRepo('XLite\Model\Module')->findModuleByName('QSL\\CloudSearch');
            $url = $module->getInstalledURL();

            $cell[static::SCHEMA_COMMENT] = static::t(
                'CloudSearch sets default sort order to relevance',
                array('url' => $url)
            );
        }

        return $cell;
    }
}