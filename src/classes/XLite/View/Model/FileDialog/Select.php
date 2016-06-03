<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model\FileDialog;

/**
 * File select dialog model widget
 */
class Select extends \XLite\View\Model\AModel
{

    /**
     * Return object name
     *
     * @return string
     */
    public function getObject()
    {
        return \XLite\Core\Request::getInstance()->object;
    }

    /**
     * Return object identificator
     *
     * @return string
     */
    public function getObjectId()
    {
        return \XLite\Core\Request::getInstance()->objectId;
    }

    /**
     * Return file object name
     *
     * @return string
     */
    public function getFileObject()
    {
        return \XLite\Core\Request::getInstance()->fileObject;
    }

    /**
     * Return file object identificator
     *
     * @return string
     */
    public function getFileObjectId()
    {
        return \XLite\Core\Request::getInstance()->fileObjectId;
    }

    /**
     * This object will be used if another one is not pased
     *
     * @return \XLite\Model\Profile
     */
    protected function getDefaultModelObject()
    {
        return null;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\FileDialog\Select';
    }
}
