<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Form\Model;

/**
 * Theme tweaker templates list search form
 */
class Template extends \XLite\View\Form\AForm
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'theme_tweaker_template';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * Get default class name
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' validationEngine theme-tweaker-template');
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $params = array(
            'id' => \XLite\Core\Request::getInstance()->id,
        );

        if (\XLite\Core\Request::getInstance()->template) {
            $params['template'] = \XLite\Core\Request::getInstance()->template;
            $params['interface'] = \XLite\Core\Request::getInstance()->interface;
            $params['innerInterface'] = \XLite\Core\Request::getInstance()->innerInterface;
            $params['isCreate'] = true;
        }

        return $params;
    }
}
