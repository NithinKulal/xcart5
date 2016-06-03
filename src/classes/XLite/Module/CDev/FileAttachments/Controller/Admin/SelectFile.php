<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\Controller\Admin;

/**
 * Select file controller
 */
class SelectFile extends \XLite\Controller\Admin\SelectFile implements \XLite\Base\IDecorator
{
    // {{{ Add actions

    /**
     * "Upload" handler for product attachments
     *
     * @return void
     */
    protected function doActionSelectUploadProductAttachments()
    {
        $this->doActionSelectProductAttachments('loadFromRequest', array('uploaded_file'));
    }

    /**
     * "URL" handler for product images.
     *
     * @return void
     */
    protected function doActionSelectUrlProductAttachments()
    {
        $this->doActionSelectProductAttachments(
            'loadFromURL',
            array(
                \XLite\Core\Request::getInstance()->url,
                (bool) \XLite\Core\Request::getInstance()->url_copy_to_local
            )
        );
    }

    /**
     * "Local file" handler for product images.
     *
     * @return void
     */
    protected function doActionSelectLocalProductAttachments()
    {
        $file = \XLite\View\BrowseServer::getNormalizedPath(\XLite\Core\Request::getInstance()->local_server_file);

        $this->doActionSelectProductAttachments(
            'loadFromLocalFile',
            array($file)
        );
    }

    /**
     * Common handler for product attachments
     *
     * @param string $methodToLoad Method to use for getting images
     * @param array  $paramsToLoad Parameters to use in attachment getter method
     *
     * @return void
     */
    protected function doActionSelectProductAttachments($methodToLoad, array $paramsToLoad)
    {
        $productId = intval(\XLite\Core\Request::getInstance()->objectId);

        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($productId);

        if (isset($product)) {
            $attachment = new \XLite\Module\CDev\FileAttachments\Model\Product\Attachment();
            $attachment->setProduct($product);

            if (call_user_func_array(array($attachment->getStorage(), $methodToLoad), $paramsToLoad)) {

                $found = false;
                foreach ($attachment->getStorage()->getDuplicates() as $duplicate) {
                    if (
                        $duplicate instanceof \XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage
                        && $duplicate->getAttachment()->getProduct()->getProductId() == $product->getProductId()
                    ) {
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    \XLite\Core\TopMessage::addError(
                        'The same file can not be assigned to one product'
                    );

                } else {
                    $product->addAttachments($attachment);

                    \XLite\Core\Database::getEM()->persist($attachment);
                    \XLite\Core\Database::getEM()->flush();

                    \XLite\Core\TopMessage::addInfo('The attachment has been added successfully');
                }

            } elseif ('extension' == $attachment->getStorage()->getLoadError()) {
                // Forbid extension
                \XLite\Core\TopMessage::addError('The file extension is forbidden');

            } else {
                \XLite\Core\TopMessage::addError('Failed to add the attachment');
            }

        } else {
            \XLite\Core\TopMessage::addError('The product for which you attempted to upload an attachment has not been found');
        }
    }

    // }}}

    // {{{ Re-upload actions

    /**
     * Return parameters array for upload "Product" target
     *
     * @return string
     */
    protected function getParamsObjectProduct()
    {
        return array(
            'target'     => 'product',
            'page'       => \XLite\Core\Request::getInstance()->fileObject,
            'product_id' => \XLite\Core\Request::getInstance()->objectId,
        );
    }

     /**
     * Return parameters array for reupload "Product" target
     *
     * @return string
     */
    protected function getParamsObjectAttachment()
    {
        $attachment = $this->getAttachment();
        return array(
            'target'     => 'product',
            'page'       => \XLite\Core\Request::getInstance()->fileObject,
            'product_id' => $attachment->product->product_id,
        );
    }

    /**
     * Get attachment 
     * 
     * @return \XLite\Module\CDev\FileAttachments\Model\Product\Attachment
     */
    protected function getAttachment()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\CDev\FileAttachments\Model\Product\Attachment')
            ->find(\XLite\Core\Request::getInstance()->objectId);
    }

    /**
     * "Upload" handler for product attachments
     *
     * @return void
     */
    protected function doActionSelectUploadAttachmentAttachments()
    {
        $this->doActionSelectAttachmentAttachments('loadFromRequest', array('uploaded_file'));
    }

    /**
     * "URL" handler for product images.
     *
     * @return void
     */
    protected function doActionSelectUrlAttachmentAttachments()
    {
        $this->doActionSelectProductAttachments(
            'loadFromURL',
            array(
                \XLite\Core\Request::getInstance()->url,
                (bool) \XLite\Core\Request::getInstance()->url_copy_to_local
            )
        );
    }

    /**
     * "Local file" handler for product images.
     *
     * @return void
     */
    protected function doActionSelectLocalAttachmentAttachments()
    {
        $file = \XLite\View\BrowseServer::getNormalizedPath(\XLite\Core\Request::getInstance()->local_server_file);

        $this->doActionSelectAttachmentAttachments(
            'loadFromLocalFile',
            array($file)
        );
    }

    /**
     * Common handler for product attachments
     *
     * @param string $methodToLoad Method to use for getting images
     * @param array  $paramsToLoad Parameters to use in attachment getter method
     *
     * @return void
     */
    protected function doActionSelectAttachmentAttachments($methodToLoad, array $paramsToLoad)
    {
        $attachment = $this->getAttachment();

        if (isset($attachment)) {
            $storage = $attachment->getStorage();
            $storage->setFilename('');
            if (call_user_func_array(array($storage, $methodToLoad), $paramsToLoad)) {
                \XLite\Core\Database::getEM()->flush();
                \XLite\Core\TopMessage::addInfo(
                    'The attachment has been successfully re-upload'
                );

            } else {
                \XLite\Core\TopMessage::addError(
                    'Failed to re-upload attachment'
                );
            }

        } else {
            \XLite\Core\TopMessage::addError(
                'Failed to re-upload attachment'
            );
        }
    }

    /**
     * Get redirect target
     *
     * @return string
     */
    protected function getRedirectTarget()
    {
        $target = parent::getRedirectTarget();

        if ('attachment' == $target) {
            $target = 'product';
        }

        return $target;
    }

    // }}}
}
