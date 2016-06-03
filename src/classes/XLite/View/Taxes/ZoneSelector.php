<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Taxes;

/**
 * Zone selector 
 */
class ZoneSelector extends \XLite\View\AView
{
    /**
     * Widget parameters names
     */
    const PARAM_FIELD_NAME = 'field';
    const PARAM_VALUE      = 'value';

    /**
     * Get all zones
     *
     * @return array
     */
    public function getZones()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Zone')->findAllZones();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'taxes/zone_selector.twig';
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
            self::PARAM_VALUE      => new \XLite\Model\WidgetParam\TypeObject('Value', null, false, '\XLite\Model\Zone'),
        );
    }

    /**
     * Check - specified zone is selected or not
     * 
     * @param \XLite\Model\Zone $current Zone
     *
     * @return boolean
     */
    protected function isSelectedZone(\XLite\Model\Zone $current)
    {
        return $this->getParam(self::PARAM_VALUE)
            && $current->getZoneId() == $this->getParam(self::PARAM_VALUE)->getZoneId();
    }
}

