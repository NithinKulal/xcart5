<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Profile list controller
 */
class ProfileList extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage users');
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Search for users');
    }

    /**
     * doActionDelete
     *
     * @return void
     */
    protected function doActionDelete()
    {
        // :TODO: check if it's really needed
    }

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass() ?: '\XLite\View\ItemsList\Model\Profile';
    }

    // {{{ Search

    /**
     * Process 'no action'
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (\XLite\Core\Request::getInstance()->fast_search) {

            // Refresh search parameters from request
            $this->fillSearchValuesStorage();

            // Get ItemsList widget
            $widget = $this->getItemsList();

            // Search for single profile
            $entity = $widget->searchForSingleEntity();

            if ($entity && $entity instanceOf \XLite\Model\Profile) {
                // Prepare redirect to profile page
                $url = $this->buildURL('profile', '', array('profile_id' => $entity->getProfileId()));
                $this->setReturnURL($url);
            }
        }
    }

    /**
     * Save search conditions
     *
     * @return void
     */
    protected function doActionSearchItemsList()
    {
        parent::doActionSearchItemsList();

        $this->setReturnURL($this->getURL(array('searched' => 1)));
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getSessionSearchConditions()
    {
        return $this->postProcessSearchParams(
            parent::getSessionSearchConditions()
        );
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        return $this->postProcessSearchParams(
            parent::getSearchParams()
        );
    }

    /**
     * Postprocess search parameters
     *
     * @param array $params Array of search parameters
     *
     * @return array
     */
    protected function postProcessSearchParams($params)
    {
        if (empty($params[\XLite\View\ItemsList\Model\Profile::PARAM_COUNTRY])) {
            // Country value is empty: make state and custom state values are empty as well
            $params[\XLite\View\ItemsList\Model\Profile::PARAM_STATE] = '';
            $params[\XLite\View\ItemsList\Model\Profile::PARAM_CUSTOM_STATE] = '';
        } else {
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find(
                $params[\XLite\View\ItemsList\Model\Profile::PARAM_COUNTRY]
            );
            if (!$country || !$country->hasStates()) {
                $params[\XLite\View\ItemsList\Model\Profile::PARAM_STATE] = '';
            }
            if (!$country || $country->hasStates()) {
                $params[\XLite\View\ItemsList\Model\Profile::PARAM_CUSTOM_STATE] = '';
            }
        }

        return $params;
    }

    // }}}
}
