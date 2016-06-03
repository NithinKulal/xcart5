<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Abstract widgets collection container
 *
 */
abstract class AWidgetsCollection extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'widgets_collection';

        return $list;
    }

    /**
     * Register the view classes collection
     *
     * @return array
     */
    abstract protected function defineWidgetsCollection();

    /**
     * Get the view classes collection
     *
     * @return array
     */
    public function getWidgetsCollection()
    {
        $list = array();

        foreach ($this->defineWidgetsCollection() as $name) {
            if ($this->isAllowedWidget($name)) {
                $list[] = $name;
            }
        }

        return $list;
    }

    /**
     * Check - allowed display subwidget or not
     * 
     * @param string $name Widget class name
     *  
     * @return boolean
     */
    protected function isAllowedWidget($name)
    {
        return true;
    }

    /**
     * Do not use the template engine
     *
     * @return null
     */
    protected function getDefaultTemplate()
    {
        return null;
    }

}
