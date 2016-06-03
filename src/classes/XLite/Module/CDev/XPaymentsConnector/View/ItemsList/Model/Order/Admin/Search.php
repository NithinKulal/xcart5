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
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\ItemsList\Model\Order\Admin;

/**
 * Search order
 */
class Search extends \XLite\View\ItemsList\Model\Order\Admin\Search implements \XLite\Base\IDecorator
{
    const SORT_BY_MODE_FRAUD =  'p.fraud';
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
		$columns = parent::defineColumns();
		
		$columns['fraud_status_xpc'] = array(
            static::COLUMN_NAME     => '',
            static::COLUMN_SORT     => static::SORT_BY_MODE_FRAUD,
            static::COLUMN_LINK     => 'order',
            static::COLUMN_TEMPLATE => 'modules/CDev/XPaymentsConnector/order/fraud_status/status.tpl',
			static::COLUMN_ORDERBY  => 350,
		);

		return $columns;
	}

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/XPaymentsConnector/order/style.css';

        return $list;
    }

    /**
     * Get column value
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model
     *
     * @return mixed
     */
    protected function getColumnClass(array $column, \XLite\Model\AEntity $entity = null)
    {
        $result = parent::getColumnClass($column, $entity);

        if ('fraud_status_xpc' == $column[static::COLUMN_CODE]) {
            $result = 'fraud-status-' . $entity->getFraudStatusXpc();
        }

        return $result;
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function getFraudInfoXpcLink(\XLite\Model\AEntity $entity)
    {
        $result = \XLite\Core\Converter::buildURL(
            'order',
            '',
            array('order_number' => $entity->getOrderNumber())
        );

        $result .= '#' . $entity->getFraudInfoXpcAnchor();

        return $result;
    }

    /**
     * Get column value
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model
     *
     * @return mixed
     */
    protected function getFraudInfoXpcTitle(\XLite\Model\AEntity $entity)
    {
        return $entity->getFraudStatusXpc();
    }
}
