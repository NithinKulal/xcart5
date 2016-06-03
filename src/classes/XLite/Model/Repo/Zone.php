<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Zone repository
 */
class Zone extends \XLite\Model\Repo\ARepo
{
    /**
     * Common search parameters
     */
    
    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'is_default';

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SECONDARY;

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('zone_name'),
    );

    // {{{ defineCacheCells

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();

        $list['all'] = array(
            self::RELATION_CACHE_CELL => array('\XLite\Model\Zone'),
        );

        $list['default'] = array(
            self::RELATION_CACHE_CELL => array('\XLite\Model\Zone'),
        );

        $list['zone'] = array(
            self::ATTRS_CACHE_CELL    => array('zone_id'),
            self::RELATION_CACHE_CELL => array('\XLite\Model\Zone'),
        );

        return $list;
    }

    // }}}

    // {{{ findAllZones

    /**
     * findAllZones
     *
     * @return array
     */
    public function findAllZones()
    {
        $data = $this->getFromCache('all');

        if (!isset($data)) {
            $data = $this->defineFindAllZones()->getResult();
            $this->saveToCache($data, 'all');
        }

        return $data;
    }

    /**
     * defineGetZones
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindAllZones()
    {
        return $this->createQueryBuilder()
            ->addSelect('ze')
            ->leftJoin('z.zone_elements', 'ze')
            ->addOrderBy('z.is_default', 'DESC')
            ->addOrderBy('z.zone_name');
    }

    // }}}

    // {{{ findZone

    /**
     * findZone
     *
     * @param integer $zoneId Zone Id
     *
     * @return \XLite\Model\Zone
     */
    public function findZone($zoneId)
    {
        $data = $this->getFromCache('zone', array('zone_id' => $zoneId));

        if (!isset($data)) {
            $data = $this->defineFindZone($zoneId)->getSingleResult();

            if ($data) {
                $this->saveToCache($data, 'zone', array('zone_id' => $zoneId));
            }
        }

        return $data;
    }

    /**
     * defineGetZone
     *
     * @param mixed $zoneId Zone id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindZone($zoneId)
    {
        return $this->createQueryBuilder()
            ->addSelect('ze')
            ->leftJoin('z.zone_elements', 'ze')
            ->andWhere('z.zone_id = :zoneId')
            ->setParameter('zoneId', $zoneId);
    }

    // }}}

    // {{{ findApplicableZones

    /**
     * Get the zones list applicable to the specified address
     *
     * @param array $address Address data
     *
     * @return array
     */
    public function findApplicableZones($address)
    {
        if (is_numeric($address['state'])) {
            $address['state'] = \XLite\Core\Database::getRepo('XLite\Model\State')->getCodeById($address['state']);
        }

        // Get all zones list
        $allZones = $this->findAllZones();
        $applicableZones = array();

        // Get the list of zones that are applicable for address
        /** @var \XLite\Model\Zone $zone */
        foreach ($allZones as $zone) {
            $zoneWeight = $zone->getZoneWeight($address);

            if (0 < $zoneWeight) {
                $applicableZones[] = array(
                    'weight' => $zoneWeight,
                    'zone' => $zone,
                );
            }
        }

        // Sort zones list by weight in reverse order
        usort($applicableZones, function ($a, $b) {
            return $a['weight'] == $b['weight']
                ? 0
                : (($a['weight'] > $b['weight']) ? -1 : 1);
        });

        $result = array();
        foreach ($applicableZones as $zone) {
            $result[] = $zone['zone'];
        }

        return $result;
    }

    /**
     * Return default zone
     *
     * @return \XLite\Model\Zone
     */
    protected function getDefaultZone()
    {
        $result = $this->getFromCache('default');

        if (!isset($result)) {
            $result = $this->findOneBy(array('is_default' => 1));
            $this->saveToCache($result, 'default');
        }

        return $result;
    }

    // }}}

    // {{{ Zones list for offline shipping

    /**
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return array
     */
    public function getOfflineShippingZones($method)
    {
        $allZones = $this->findAllZones();
        $usedZones = $this->getOfflineShippingUsedZones($method);

        $usedList = array();
        $unUsedList = array();

        if ($usedZones) {
            foreach ($allZones as $zone) {
                if (isset($usedZones[$zone->getZoneId()])) {
                    $usedList[$zone->getZoneId()] = sprintf('%s (%d)', $zone->getZoneName(), $usedZones[$zone->getZoneId()]);

                } else {
                    $unUsedList[$zone->getZoneId()] = sprintf('%s (%d)', $zone->getZoneName(), 0);
                }
            }

            if ($usedList) {
                asort($usedList);
                asort($unUsedList);
            }
        } else {
            foreach ($allZones as $zone) {
                $unUsedList[$zone->getZoneId()] = $zone->getZoneName();
            }
        }

        return array($usedList, $unUsedList);
    }

    /**
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return array
     */
    protected function getOfflineShippingUsedZones($method)
    {
        $list = array();

        if ($method->getShippingMarkups()) {
            foreach ($method->getShippingMarkups() as $markup) {
                if ($markup->getZone()) {
                    if (!isset($list[$markup->getZone()->getZoneId()])) {
                        $list[$markup->getZone()->getZoneId()] = 1;

                    } else {
                        $list[$markup->getZone()->getZoneId()]++;
                    }
                }
            }
        }

        return $list;
    }

    // }}}
}
