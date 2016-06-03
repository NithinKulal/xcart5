<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View\Product;

/**
 * Form checkbox
 *
 * @ListChild (list="product.attachments.properties", weight="300", zone="admin")
 */
class PrivateControl extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_ATTACHMENT = 'attachment';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ATTACHMENT => new \XLite\Model\WidgetParam\TypeObject(
                'Attachment', null, false, '\XLite\Module\CDev\FileAttachments\Model\Product\Attachment'
            ),
        );
    }

    /**
     * Check if this widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->isLocalStored();
    }

    /**
     * Returns true if attachment is stored locally
     *
     * @return boolean
     */
    protected function isLocalStored()
    {
        $attachment = $this->getParam(self::PARAM_ATTACHMENT);
        return $attachment
            && $attachment->getStorage()
            && $attachment->getStorage()->getStorageType()
               !== \XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage::STORAGE_URL;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Egoods/product/parts/properties.check.twig';
    }

    /**
     * Get module id 
     * 
     * @return string
     */
    protected function getModuleId()
    {
        $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->findOneBy(array('author' => 'CDev', 'name' => 'Egoods'));

        return $module->getModuleId();
    }
}

