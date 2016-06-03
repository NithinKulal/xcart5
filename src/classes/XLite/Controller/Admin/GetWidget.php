<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Get widget (AJAX)
 * TODO: multiple inheritance required;
 * todo: deprecated, check and remove
 */
class GetWidget extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Handles the request. Parses the request variables if necessary. Attempts to call the specified action function
     *
     * @return void
     */
    public function handleRequest()
    {
        $request = \XLite\Core\Request::getInstance();

        foreach ($this->getAJAXParamsTranslationTable() as $ajaxParam => $requestParam) {
            if (!empty($request->$ajaxParam)) {
                $request->$requestParam = $request->$ajaxParam;
                $this->set($requestParam, $request->$ajaxParam);
            }
        }

        parent::handleRequest();
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && $this->checkRequest()
            && \XLite\Core\Operator::isClassExists($this->getClass());
    }

    /**
     * Return Viewer object
     *
     * @return \XLite\View\Controller
     */
    public function getViewer($isExported = false)
    {
        return parent::getViewer(true);
    }

    /**
     * Get class name
     *
     * @return string
     */
    public function getClass()
    {
        return \XLite\Core\Request::getInstance()->{self::PARAM_AJAX_CLASS};
    }


    /**
     * These params from AJAX request will be translated into the corresponding ones
     *
     * @return array
     */
    protected function getAJAXParamsTranslationTable()
    {
        return array(
            self::PARAM_AJAX_TARGET => 'target',
            self::PARAM_AJAX_ACTION => 'action',
        );
    }

    /**
     * checkRequest
     *
     * @return boolean
     */
    protected function checkRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH'];
    }

    /**
     * Select template to use
     *
     * @return string
     */
    protected function getViewerTemplate()
    {
        return 'get_widget.twig';
    }
}
