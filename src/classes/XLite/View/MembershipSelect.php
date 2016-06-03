<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Membership selection widget
 *
 * @deprecated Unused class
 */
class MembershipSelect extends \XLite\View\FormField
{
    /**
     * Widget parameters names
     */
    const PARAM_FIELD_NAME = 'field';
    const PARAM_VALUE      = 'value';
    const PARAM_ALL_OPTION = 'allOption';
    const PARAM_PENDING_OPTION = 'pendingOption';

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
        return 'common/select_membership.twig';
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
            static::PARAM_FIELD_NAME     => new \XLite\Model\WidgetParam\TypeString('Field', 'membership', false),
            static::PARAM_VALUE          => new \XLite\Model\WidgetParam\TypeString('Value', '%', false),
            static::PARAM_ALL_OPTION     => new \XLite\Model\WidgetParam\TypeBool('Display All option', false, false),
            static::PARAM_PENDING_OPTION => new \XLite\Model\WidgetParam\TypeBool('Display Pending option', false, false),
        );
    }
}
