<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2015 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\QSL\CloudSearch\Model\Repo;

/**
 * 
 * 
 * @LC_Dependencies("CDev\SimpleCMS")
 */
abstract class Page extends \XLite\Module\CDev\SimpleCMS\Model\Repo\Page implements \XLite\Base\IDecorator
{
    const PARAM_ENABLED = 'enabled';
    
    /**
     * Return list of handling search params
     *
     * @return array
     */
    protected function getHandlingSearchParams()
    {
        $list = parent::getHandlingSearchParams();

        $list[] = static::PARAM_ENABLED;

        return $list;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder
            ->andWhere('p.enabled = :enabled')
            ->setParameter('enabled', $value);
    }
}
