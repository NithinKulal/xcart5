<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form;

/**
 * Settings dialog form
 */
class Settings extends \XLite\View\Form\AForm
{
    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * Ability to add the 'enctype="multipart/form-data"' form attribute
     *
     * @return boolean
     */
    protected function isMultipart()
    {
        return true;
    }

    /**
     * getDefaultClassName
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        $class = parent::getDefaultClassName();

        $class .= ($class ? ' ' : '') . 'validationEngine';

        return $class;
    }

    /**
     * Required form parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();

        $list['page'] = $this->page;

        if ('module' == \XLite\Core\Request::getInstance()->target) {
            $list['moduleId'] = \XLite\Core\Request::getInstance()->moduleId;
        }

        if (\XLite\Core\Request::getInstance()->return) {
            $list['return'] = \XLite\Core\Request::getInstance()->return;
        }

        return $list;
    }

}
