<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;
use XLite\View\ItemsList\Model\State;

/**
 * States management page controller
 */
class States extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (!\XLite\Core\Request::getInstance()->{State::PARAM_COUNTRY_CODE}) {
            $this->setReturnURL($this->buildURL('states', '', [State::PARAM_COUNTRY_CODE => $this->getCountryCode()]));
        }
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('States');
    }

    /**
     * Get current country code
     *
     * @return string
     */
    public function getCountryCode()
    {
        return \XLite\Core\Request::getInstance()->{State::PARAM_COUNTRY_CODE}
            ?: \XLite\Core\Config::getInstance()->Company->location_country
                ?: 'US';
    }

    /**
     * Get session cell name for pager widget
     *
     * @return string
     */
    public function getPagerSessionCell()
    {
        return parent::getPagerSessionCell() . '_' . $this->getCountryCode();
    }

    /**
     * Get list of countries which has states
     *
     * @return array
     */
    public function getCountriesWithStates()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Country::P_HAS_STATES} = true;

        return \XLite\Core\Database::getRepo('XLite\Model\Country')->search($cnd);
    }

    /**
     * doActionUpdate
     *
     * @return void
     */
    protected function doActionUpdateItemsList()
    {
        parent::doActionUpdateItemsList();

        \XLite\Core\Database::getRepo('XLite\Model\State')->cleanCache();
        \XLite\Core\Database::getRepo('XLite\Model\Region')->cleanCache();
    }

}
