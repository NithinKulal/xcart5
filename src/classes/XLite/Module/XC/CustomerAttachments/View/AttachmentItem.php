<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\View;

/**
 * Row in attachment list
 */
class AttachmentItem extends \XLite\View\AView
{
    /**
     * Widget param
     */
    const PARAM_ATTACHMENT = 'attachment';

    /**
     * Return widget default template
     *
     * @return string
     */
    public function getDefaultTemplate()
    {
        return 'modules/XC/CustomerAttachments/attachment_item.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_ATTACHMENT => new \XLite\Model\WidgetParam\TypeObject('Item attachment', null, false, 'XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment'),
        );
    }

    /**
     * Get attachment
     *
     * @return \XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment
     */
    protected function getAttachment()
    {
        return $this->getParam(static::PARAM_ATTACHMENT);
    }


} 