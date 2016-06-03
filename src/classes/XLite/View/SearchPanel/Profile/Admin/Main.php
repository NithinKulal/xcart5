<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SearchPanel\Profile\Admin;

/**
 * Main admin profile search panel
 */
class Main extends \XLite\View\SearchPanel\Profile\Admin\AAdmin
{

    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'profiles/parts/search_form/style.css';

        return $list;
    }

    /**
     * Get JS files list
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'profiles/parts/search_form/date_type.js';

        return $list;
    }

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Profiles\Search';
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.profiles';
    }

    /**
     * Define conditions
     *
     * @return array
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + array(
            'pattern' => array(
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Enter keyword'),
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ),
            'membership' => array(
                static::CONDITION_CLASS => '\XLite\View\FormField\Select\CheckboxList\MembershipSearch',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ),
            'user_type' => array(
                static::CONDITION_CLASS => '\XLite\View\FormField\Select\CheckboxList\UserType',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ),
            'status' => array(
                static::CONDITION_CLASS => '\XLite\View\FormField\Select\AccountStatusSearch',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ),
        );
    }

    /**
     * Define hidden conditions
     *
     * @return array
     */
    protected function defineHiddenConditions()
    {
        return parent::defineHiddenConditions() + array(
            'country' => array(
                static::CONDITION_CLASS => '\XLite\View\FormField\Select\Country',
                \XLite\View\FormField\AFormField::PARAM_LABEL                 => static::t('Country'),
                \XLite\View\FormField\AFormField::PARAM_VALUE                 => $this->getCondition('country') ?: '',
                \XLite\View\FormField\Select\Country::PARAM_STATE_SELECTOR_ID => 'stateSelectorId',
                \XLite\View\FormField\Select\Country::PARAM_STATE_INPUT_ID    => 'stateBoxId',
            ),
            'state'   => array(
                static::CONDITION_CLASS => '\XLite\View\FormField\Select\State',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('State'),
                \XLite\View\FormField\AFormField::PARAM_ID    => 'stateSelectorId',
            ),
            'customState'   => array(
                static::CONDITION_CLASS => '\XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('State'),
                \XLite\View\FormField\AFormField::PARAM_ID    => 'stateBoxId',
            ),
            'address' => array(
                static::CONDITION_CLASS => '\XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('Address'),
            ),
            'phone' => array(
                static::CONDITION_CLASS => '\XLite\View\FormField\Input\Text\Phone',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('Phone'),
            ),
            'date_type'   => array(
                static::CONDITION_TEMPLATE => 'profiles/parts/search_form/date_type.twig',
            ),
        );
    }

    /**
     * Check - specified date type selected or not
     *
     * @param string $type Date type OPTIONAL
     *
     * @return boolean
     */
    protected function isDateTypeSelected($type = null)
    {
        return (isset($type) && $this->getCondition('date_type') == $type)
            || (!isset($type) && !$this->getCondition('date_type'))
            || ('R' == $type && !$this->getCondition('date_type'));
    }
}
