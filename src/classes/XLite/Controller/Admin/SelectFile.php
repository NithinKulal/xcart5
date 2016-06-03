<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Select File controller
 */
class SelectFile extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Force return URL
     *
     * @var string
     */
    protected $forceReturnURL = null;


    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Upload file');
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isAdmin();
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return '\XLite\View\Model\FileDialog\Select';
    }

    /**
     * Return target for redirect URL. Object string should contain the target name
     *
     * @return string
     */
    protected function getObjectTarget()
    {
        return $this->getModelForm()->getObject();
    }

    /**
     * Return array with parameters for redirect URL. Inner method.
     *
     * @return array
     */
    protected function getParamsObject()
    {
        $methodParamsObject = 'getParamsObject' . \XLite\Core\Converter::convertToCamelCase($this->getObjectTarget());

        return method_exists($this, $methodParamsObject)
            ? call_user_func(array($this, $methodParamsObject))
            : array();
    }

    /**
     * Main 'Select' action handler.
     *  - calls "doActionSelect<file_select_action><object><file_object>()" handler
     *  - set return URL which is set by "getParamsObject<object>()" getter
     *
     * @return void
     */
    protected function doActionSelect()
    {
        $actionName = 'doActionSelect'
            . \XLite\Core\Converter::convertToCamelCase(\XLite\Core\Request::getInstance()->file_select)
            . \XLite\Core\Converter::convertToCamelCase($this->getObjectTarget())
            . \XLite\Core\Converter::convertToCamelCase(\XLite\Core\Request::getInstance()->fileObject);

        if (method_exists($this, $actionName)) {
            call_user_func(array($this, $actionName));
        }

        $returnURL = $this->forceReturnURL
            ?: $this->buildURL(
                $this->getRedirectTarget(),
                '',
                $this->getParamsObject()
            );

        $this->setReturnURL($returnURL);
    }

    protected function doNoAction()
    {
        $request = \XLite\Core\Request::getInstance();
        $server = $request->getServerData();
        $size = \XLite\Core\Converter::convertShortSize(ini_get('post_max_size'));

        if (isset($server['CONTENT_LENGTH']) && $server['CONTENT_LENGTH'] > $size) {
            \XLite\Core\TopMessage::addError(
                'File size exceeds the maximum size',
                array('size' => \XLite\Core\Converter::convertShortSizeToHumanReadable($size))
            );

            $this->setReturnURL(
                $this->getReferrerURL()
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
        return $this->getObjectTarget();
    }

    // {{{ Import

    /**
     * Common handler for import
     *
     * @param string $methodToLoad Method to use for getting file
     * @param array  $paramsToLoad Parameters to use in getter method
     *
     * @return void
     */
    protected function doActionSelectImport($methodToLoad, array $paramsToLoad)
    {
        \XLite\Core\Session::getInstance()->importCell = null;
        $methodToLoad .= 'Import';

        $path = call_user_func_array(array($this, $methodToLoad), $paramsToLoad);
        if (is_array($path)) {
            if (!$path[0] && $path[1]) {
                \XLite\Core\TopMessage::addError($path[1]);
            }

            $path = $path[0];
        }

        if ($path) {
            chmod($path, 0644);
            \XLite\Core\Session::getInstance()->importCell = array(
                'path'     => $path,
                'position' => 0,
            );
        }
    }

    /**
     * Load import file from request
     *
     * @param string $key Request key
     *
     * @return array
     */
    protected function loadFromRequestImport($key)
    {
        $result = null;
        $error = null;
        $message = null;

        $cell = isset($_FILES[$key]) ? $_FILES[$key] : null;
        if ($cell) {
            $size = null;
            switch ($cell['error']) {
                case UPLOAD_ERR_OK:
                    $path = \Includes\Utils\FileManager::getUniquePath(LC_DIR_TMP, $cell['name']);
                    if (move_uploaded_file($cell['tmp_name'], $path)) {
                        $result = $path;
                    }
                    break;

                case UPLOAD_ERR_INI_SIZE:
                    $size = ini_get('upload_max_filesize');

                case UPLOAD_ERR_FORM_SIZE:
                    $size = $size ?: \XLite\Core\Request::getInstance()->MAX_FILE_SIZE;
                    $error = 'File size exceeds the maximum size (' . $size . ')';
                    $size = \XLite\Core\Converter::convertShortSizeToHumanReadable($size);
                    $message = \XLite\Core\Translation::lbl(
                        'File size exceeds the maximum size',
                        array('size' => $size)
                    );
                    break;

                case UPLOAD_ERR_PARTIAL:
                    $error = 'The uploaded file was only partially uploaded';

                case UPLOAD_ERR_NO_FILE:
                    $error = $error ?: 'No file was uploaded';

                case UPLOAD_ERR_NO_TMP_DIR:
                    $error = $error ?: 'Missing a temporary folder';

                case UPLOAD_ERR_CANT_WRITE:
                    $error = $error ?: 'Failed to write file to disk';

                case UPLOAD_ERR_EXTENSION:
                    $message = \XLite\Core\Translation::lbl(
                        'The file was not loaded because of a failure on the server.'
                    );
                    $error = $error ?: 'File upload stopped by extension';
                    break;

                default:
            }
        }

        if ($result && $message) {
            \XLite\Logger::getInstance()->log('Upload file error: ' . $error ?: $message, LOG_ERR);
        }

        return array($result, $message);
    }

    /**
     * Load import file from local file system
     *
     * @param string $path Local path
     *
     * @return string
     */
    protected function loadFromLocalFileImport($path)
    {
        $result = null;

        $tmpPath = \Includes\Utils\FileManager::getUniquePath(LC_DIR_TMP, basename($path));
        if (copy($path, $tmpPath)) {
            $result = $tmpPath;
        }

        return $result;
    }

    /**
     * Load import file from URL
     * fixme: $path is undefined
     *
     * @param string $url URL
     *
     * @return string
     */
    protected function loadFromURLImport($url)
    {
        $result = null;

        $content = \XLite\Core\Operator::getURLContent($url);
        $path = \Includes\Utils\FileManager::getUniquePath(LC_DIR_TMP, basename($path));
        if ($content && file_put_contents($path, $content)) {
            $result = $path;
        }

        return $result;
    }

    // }}}

    // {{{ Import language from CSV file

    /**
     * Common handler for language import.
     *
     * @param string $path    Path
     * @param string $message Message OPTIONAL
     *
     * @return void
     */
    protected function doActionSelectLanguageFile($path, $message = null)
    {
        if ($path && !$message) {
            \XLite\Core\Session::getInstance()->language_import_file = $path;

        } else {
            \XLite\Core\TopMessage::addError($message);
        }

        $this->forceReturnURL = $this->buildURL('languages');
    }

    /**
     * "Upload" handler for category images.
     *
     * @return void
     */
    protected function doActionSelectUploadLanguageFile()
    {
        $result = null;
        $error = null;
        $message = null;

        $key = 'uploaded_file';

        $cell = isset($_FILES[$key]) ? $_FILES[$key] : null;

        if ($cell) {
            $size = null;

            switch ($cell['error']) {

                case UPLOAD_ERR_OK:
                    $path = \Includes\Utils\FileManager::getUniquePath(LC_DIR_TMP, $cell['name']);
                    if (move_uploaded_file($cell['tmp_name'], $path)) {
                        $result = $path;
                    }
                    break;

                case UPLOAD_ERR_INI_SIZE:
                    $size = ini_get('upload_max_filesize');

                case UPLOAD_ERR_FORM_SIZE:
                    $size = $size ?: \XLite\Core\Request::getInstance()->MAX_FILE_SIZE;
                    $error = 'File size exceeds the maximum size (' . $size . ')';
                    $size = \XLite\Core\Converter::convertShortSizeToHumanReadable($size);
                    $message = \XLite\Core\Translation::lbl(
                        'File size exceeds the maximum size',
                        array('size' => $size)
                    );
                    break;

                case UPLOAD_ERR_PARTIAL:
                    $error = 'The uploaded file was only partially uploaded';

                case UPLOAD_ERR_NO_FILE:
                    $error = $error ?: 'No file was uploaded';

                case UPLOAD_ERR_NO_TMP_DIR:
                    $error = $error ?: 'Missing a temporary folder';

                case UPLOAD_ERR_CANT_WRITE:
                    $error = $error ?: 'Failed to write file to disk';

                case UPLOAD_ERR_EXTENSION:
                    $message = \XLite\Core\Translation::lbl(
                        'The file was not loaded because of a failure on the server.'
                    );
                    $error = $error ?: 'File upload stopped by extension';
                    break;

                default:
            }
        }

        if ($result && $message) {
            \XLite\Logger::getInstance()->log('Upload file error: ' . $error ?: $message, LOG_ERR);
        }

        $this->doActionSelectLanguageFile($result, $message);
    }

    /**
     * "URL" handler for category images.
     * Returns path of the temporary file
     * fixme: $path is undefined
     *
     * @return void
     */
    protected function doActionSelectUrlLanguageFile()
    {
        $result = null;

        $url = \XLite\Core\Request::getInstance()->url;

        $content = \XLite\Core\Operator::getURLContent($url);

        $path = \Includes\Utils\FileManager::getUniquePath(LC_DIR_TMP, basename($path));

        if ($content && file_put_contents($path, $content)) {
            $result = $path;
        }

        $this->doActionSelectLanguageFile($result);
    }

    /**
     * "Local file" handler for category images.
     * Returns path of the temporary file
     *
     * @return void
     */
    protected function doActionSelectLocalLanguageFile()
    {
        $result = null;

        $path = \XLite\View\BrowseServer::getNormalizedPath(\XLite\Core\Request::getInstance()->local_server_file);

        $tmpPath = \Includes\Utils\FileManager::getUniquePath(LC_DIR_TMP, basename($path));

        if (copy($path, $tmpPath)) {
            $result = $tmpPath;
        }

        $this->doActionSelectLanguageFile($result);
    }

    // }}}
}
