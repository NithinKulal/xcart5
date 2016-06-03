<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\Controller\Admin;

/**
 * Product controller
 */
class Product extends \XLite\Controller\Admin\Product implements \XLite\Base\IDecorator
{
    // {{{ Pages

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        if (!$this->isNew()) {
            $list['attachments'] = static::t('Attachments');
        }

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        if (!$this->isNew()) {
            $list['attachments'] = 'modules/CDev/FileAttachments/product_tab.twig';
        }

        return $list;
    }

    // }}}

    /**
     * Remove file
     *
     * @return void
     */
    protected function doActionRemoveAttachment()
    {
        $attachment = \XLite\Core\Database::getRepo('XLite\Module\CDev\FileAttachments\Model\Product\Attachment')
            ->find(\XLite\Core\Request::getInstance()->id);

        if ($attachment) {
            $attachment->getProduct()->getAttachments()->removeElement($attachment);
            \XLite\Core\Database::getEM()->remove($attachment);
            \XLite\Core\TopMessage::addInfo('Attachment has been deleted successfully');
            $this->setPureAction(true);

        } else {
            $this->valid = false;
            \XLite\Core\TopMessage::addError('Attachment is not deleted');
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Update files
     *
     * @return void
     */
    protected function doActionUpdateAttachments()
    {
        $changed = false;

        $data = \XLite\Core\Request::getInstance()->data;
        if ($data && is_array($data)) {
            $repository = \XLite\Core\Database::getRepo('XLite\Module\CDev\FileAttachments\Model\Product\Attachment');
            foreach ($data as $id => $row) {
                $attachment = $repository->find($id);

                if ($attachment) {
                    $attachment->map($row);
                    $changed = true;
                }
            }
        }

        if ($changed) {
            \XLite\Core\TopMessage::addInfo('Attachments have been updated successfully');
        }

        \XLite\Core\Database::getEM()->flush();
    }
}
