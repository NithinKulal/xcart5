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
 * @copyright Copyright (c) 2011-2014 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\QSL\CloudSearch\Controller\Admin;

/**
 * CloudSearch dashboard page controller (Promotion section)
 */
class Promotions extends \XLite\Controller\Admin\Promotions implements \XLite\Base\IDecorator
{
    /**
     * Page key
     */
    const PAGE_CLOUD_SEARCH_DASHBOARD = 'cloud_search';


    /**
     * Get pages static
     *
     * @return array
     */
    public static function getPagesStatic()
    {
        $list = parent::getPagesStatic();

        $list[static::PAGE_CLOUD_SEARCH_DASHBOARD] = array(
            'name' => static::t('CloudSearch'),
            'tpl'  => 'modules/QSL/CloudSearch/promotions_menu_body.tpl',
        );

        return $list;
    }
}
