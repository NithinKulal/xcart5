<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * File upload controller
 */
class Files extends \XLite\Controller\Admin\AAdmin
{
    const RESPONSE_WIDGET = 'widget';
    const RESPONSE_JSON = 'json';

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && $this->isAJAX();
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return true;
    }

    /**
     * Checks file
     *
     * @param mixed $file File
     *
     * @return void
     */
    protected function checkFile($file)
    {
        if ($file
            && \XLite\Core\Request::getInstance()->is_image
            && !$file->isImage()
        ) {
            $file->removeFile();
            $this->sendResponse(null, static::t('File is not an image'));
        }
    }

    /**
     * Uploads file from form data.
     * Uses 'file' request form value.
     *
     * @return void
     */
    protected function doActionUploadFromFile()
    {
        $file = new \XLite\Model\TemporaryFile();
        $message = '';
        if ($file->loadFromRequest('file')) {
            $this->checkFile($file);
            \XLite\Core\Database::getEM()->persist($file);
            \XLite\Core\Database::getEM()->flush();

        } else {
            $message = static::t('File is not uploaded');
        }

        $this->sendResponse($file, $message);
    }

    /**
     * Uploads file from URL.
     * Uses 'url' and 'copy' request value.
     *
     * @return void
     */
    protected function doActionUploadFromURL()
    {
        $file = new \XLite\Model\TemporaryFile();
        $message = '';
        if ($file->loadFromURL(\XLite\Core\Request::getInstance()->url, \XLite\Core\Request::getInstance()->copy)) {
            $this->checkFile($file);
            \XLite\Core\Database::getEM()->persist($file);
            \XLite\Core\Database::getEM()->flush();

        } else {
            $message = static::t('File is not uploaded');
        }

        $this->sendResponse($file, $message);
    }

    /**
     * Uploads file from URL.
     * Uses 'url' and 'copy' request value.
     *
     * @return void
     */
    protected function doActionRefresh()
    {
        $file = new \XLite\Model\TemporaryFile();
        if (\XLite\Core\Request::getInstance()->markAsImage) {
            $file->setWidth(1);
        }
        $this->sendResponse($file, '');
    }

    /**
     * Calls response strategy for chosen response mode
     *
     * @param \XLite\Model\TemporaryFile $file Uploaded file object
     * @param string $message Possible error message
     * @return void
     */
    protected function sendResponse($file, $message)
    {
        $mode = $this->getResponseMode();

        $strategies = $this->getResponseStrategies();

        if (in_array($mode, array_keys($strategies))) {
            call_user_func_array($strategies[$mode], array($file, $message));
        }
    }

    /**
     * Returns current response mode.
     *
     * @return string
     */
    protected function getResponseMode()
    {
        return \XLite\Core\Request::getInstance()->mode ?: static::RESPONSE_WIDGET;
    }

    /**
     * Returns possible response strategies.
     * Contains callables as array values.
     *
     * @return array
     */
    protected function getResponseStrategies()
    {
        return array(
            static::RESPONSE_WIDGET => array($this, 'sendResponseAsWidget'),
            static::RESPONSE_JSON => array($this, 'sendResponseAsJSON'),
        );
    }

    /**
     * Prints widget content
     *
     * @param  \XLite\Model\TemporaryFile $file    Image file
     * @param  string $message Possible error message
     */
    protected function sendResponseAsWidget($file, $message)
    {
        $this->getContent($file, $message);
    }

    /**
     * Prints json output with image data
     *
     * @param  \XLite\Model\TemporaryFile $file    Image file
     * @param  string $message Possible error message
     */
    protected function sendResponseAsJSON($file, $message)
    {
        $this->set('silent', true);
        $this->setSuppressOutput(true);

        if ($message || !$file) {
            $this->headerStatus(500);
            $response = array(
                'message' => $message,
            );
        } else {
            $response = $this->getSuccessResponseData($file);
        }

        $this->displayJSON($response);
    }

    /**
     * Builds image data
     *
     * @param  \XLite\Model\TemporaryFile $file    Image file
     */
    protected function getSuccessResponseData($file)
    {
        $response = array(
            'size'    => $file->getSize(),
            'width'   => $file->getWidth(),
            'height'  => $file->getHeight(),
            'url'     => $file->getFrontURL(),
            'id'      => $file->getId(),
            'message' => static::t('File was successfully uploaded'),
        );

        return $response;
    }

    /**
     * Return content
     *
     * @param mixed  $file    File
     * @param string $message Message OPTIONAL
     *
     * @return void
     */
    protected function getContent($file, $message = '')
    {
        static::sendHeaders($this->getAdditionalHeaders());

        $viewer = new \XLite\View\FileUploader(
            array(
                \XLite\View\FileUploader::PARAM_NAME         => \XLite\Core\Request::getInstance()->name,
                \XLite\View\FileUploader::PARAM_MULTIPLE     => \XLite\Core\Request::getInstance()->multiple,
                \XLite\View\FileUploader::PARAM_OBJECT       => $file,
                \XLite\View\FileUploader::PARAM_OBJECT_ID    => \XLite\Core\Request::getInstance()->object_id,
                \XLite\View\FileUploader::PARAM_MESSAGE      => $message,
                \XLite\View\FileUploader::PARAM_IS_TEMPORARY => true,
                \XLite\View\FileUploader::PARAM_MAX_WIDTH    => \XLite\Core\Request::getInstance()->max_width,
                \XLite\View\FileUploader::PARAM_MAX_HEIGHT   => \XLite\Core\Request::getInstance()->max_height,
            )
        );

        $this->printAJAXOutput($viewer);
        exit(0);
    }

    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return boolean
     */
    public static function needFormId()
    {
        return false;
    }
}
