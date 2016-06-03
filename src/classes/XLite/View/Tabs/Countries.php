<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to countries, states and zones
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Countries extends \XLite\View\Tabs\ATabs
{
    /**
     * Zone
     *
     * @var \XLite\Model\Zone
     */
    protected $zone;

    /**
     * Zones
     *
     * @var array
     */
    protected $zones;

    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'countries';
        $list[] = 'states';
        $list[] = 'zones';

        return $list;
    }

    /**
     * Check if zone details page should be displayed
     *
     * @return boolean
     */
    public function isDisplayZoneDetails()
    {
        return 'add' === \XLite\Core\Request::getInstance()->mode
            || null !== \XLite\Core\Request::getInstance()->zone_id;
    }

    /**
     * Return zone
     *
     * @return \XLite\Model\Zone
     */
    public function getZone()
    {
        if (null === $this->zone) {
            if (null !== \XLite\Core\Request::getInstance()->zone_id) {
                $zone = \XLite\Core\Database::getRepo('XLite\Model\Zone')
                    ->find(\XLite\Core\Request::getInstance()->zone_id);

                if (null === $zone) {
                    \XLite\Core\TopMessage::addError('Requested zone does not exists');

                } else {
                    $this->zone = $zone;
                }

            } else {
                $this->zone = new \XLite\Model\Zone();
            }
        }

        return $this->zone;
    }

    /**
     * getShippingZones
     *
     * @return array
     */
    public function getShippingZones()
    {
        if (null === $this->zones) {
            $this->zones = \XLite\Core\Database::getRepo('XLite\Model\Zone')->findAllZones();
        }

        return $this->zones;
    }

    /**
     * isZonesDefined
     *
     * @return boolean
     */
    public function isZonesDefined()
    {
        return count($this->getShippingZones()) > 1;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'countries' => [
                'weight'   => 100,
                'title'    => static::t('Countries'),
                'widget'    => '\XLite\View\ItemsList\Model\Country',
            ],
            'states' => [
                'weight'   => 200,
                'title'    => static::t('States'),
                'widget'    => '\XLite\View\ItemsList\Model\State',
            ],
            'zones' => [
                'weight'   => 300,
                'title'    => static::t('Zones'),
                'template' => 'zones/body.twig',
                'jsFiles'  => 'zones/details/controller.js',
                'cssFiles' => 'zones/style.css',
            ],
        ];
    }

    /**
     * Disable city masks field in the interface
     *
     * @return boolean
     */
    protected function isCityMasksEditEnabled()
    {
        return false;
    }

    /**
     * Disable address masks field in the interface
     *
     * @return boolean
     */
    protected function isAddressMasksEditEnabled()
    {
        return false;
    }
}
