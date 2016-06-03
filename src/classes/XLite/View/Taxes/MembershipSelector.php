<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Taxes;

/**
 * Membership selector 
 */
class MembershipSelector extends \XLite\View\AView
{
    /**
     * Widget parameters names
     */
    const PARAM_FIELD_NAME = 'field';
    const PARAM_VALUE      = 'value';

    /**
     * Get active memberships
     *
     * @return array
     */
    public function getMemberships()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Membership')->findActiveMemberships();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'taxes/membership_selector.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_FIELD_NAME => new \XLite\Model\WidgetParam\TypeString('Field', 'membership', false),
            self::PARAM_VALUE      => new \XLite\Model\WidgetParam\TypeObject('Value', null, false, '\XLite\Model\Membership'),
        );
    }

    /**
     * Check - specified memerbship is selected or not
     * 
     * @param \XLite\Model\Membership $current Membership
     *
     * @return boolean
     */
    protected function isSelectedMembership(\XLite\Model\Membership $current)
    {
        return $this->getParam(self::PARAM_VALUE)
            && $current->getMembershipId() == $this->getParam(self::PARAM_VALUE)->getMembershipId();
    }
}

