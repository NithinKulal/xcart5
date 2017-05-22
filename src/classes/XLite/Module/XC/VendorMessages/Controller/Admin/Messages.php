<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Controller\Admin;

/**
 * Messages
 */
class Messages extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target');

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return static::t('Messages');
    }

    /**
     * @inheritdoc
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * Check - search box is visible or not
     *
     * @return boolean
     */
    public function isSearchVisible()
    {
        return 0 < \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->count();
    }

    /**
     * Get search condition parameter by name
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return isset($searchParams[$paramName])
            ? $searchParams[$paramName]
            : null;
    }

    /**
     * @inheritdoc
     */
    public function isRedirectNeeded()
    {
        return ($this->getAction() == 'search' && !$this->silent)
            || parent::isRedirectNeeded();
    }

    /**
     * Save search conditions
     */
    protected function doActionSearch()
    {
        $cellName = \XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Admin\All::getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $searchParams = $this->getConditions();

        foreach (
            \XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Admin\All::getSearchParams() as $requestParam
        ) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $searchParams[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        return $searchParams;
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $cellName = \XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Admin\All::getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = array();
        }

        return $searchParams;
    }


}
