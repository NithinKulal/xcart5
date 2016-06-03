<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Controller\Admin;

/**
 * Select File controller
 *
 */
class SelectFile extends \XLite\Controller\Admin\SelectFile implements \XLite\Base\IDecorator
{
    /**
     * Get redirect target 
     * 
     * @return string
     */
    protected function getRedirectTarget()
    {
        $target = parent::getRedirectTarget();

        if ('import_pin_codes' == $target) {
            $target = 'add_pin_codes';
        }

        return $target;
    }

    /**
     * Return parameters array for "Import" target
     *
     * @return string
     */
    protected function getParamsObjectImportPinCodes()
    {
        return array(
            'action' => 'import',
            'product_id' => \XLite\Core\Request::getInstance()->objectId
        );
    }

    /**
     * Common handler for pin codes import
     *
     * @param string $methodToLoad Method to use for getting file
     * @param array  $paramsToLoad Parameters to use in getter method
     *
     * @return void
     */
    protected function doActionSelectImportPinCodes($methodToLoad, array $paramsToLoad)
    {
        \XLite\Core\Session::getInstance()->importPinCodesCell = null;
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
            \XLite\Core\Session::getInstance()->pinCodesImportFile = $path;
        }
    }

    /**
     * "Upload" handler for pin codes import
     *
     * @return void
     */
    protected function doActionSelectUploadImportPinCodes()
    {
        $this->doActionSelectImportPinCodes('loadFromRequest', array('uploaded_file'));
    }

    /**
     * "URL" handler for import
     *
     * @return void
     */
    protected function doActionSelectUrlImportPinCodes()
    {
        $this->doActionSelectImportPinCodes(
            'loadFromURL',
            array(
                \XLite\Core\Request::getInstance()->url,
            )
        );
    }

    /**
     * "Local file" handler for import
     *
     * @return void
     */
    protected function doActionSelectLocalImportPinCodes()
    {
        $file = \XLite\View\BrowseServer::getNormalizedPath(\XLite\Core\Request::getInstance()->local_server_file);

        $this->doActionSelectImportPinCodes(
            'loadFromLocalFile',
            array($file)
        );
    }

    // }}}
}
