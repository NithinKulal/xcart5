<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\View\ItemsList\Model\Shipping;

/**
 * Shipping methods list
 */
class Methods extends \XLite\View\ItemsList\Model\Shipping\Methods implements \XLite\Base\IDecorator
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $processor = $this->getProcessor();
        if ($processor && 'auctionInc' === $processor->getProcessorId()) {
            $columns['onDemand'] = array(
                static::COLUMN_NAME      => static::t('On-demand'),
                static::COLUMN_CLASS     => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff',
                static::COLUMN_HEAD_HELP => 'set to \'On\' to restrict service to selected products',
                static::COLUMN_ORDERBY   => 300,
            );
        }

        return $columns;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $carrierParam = \XLite\Model\Repo\Shipping\Method::P_CARRIER;
        if (!empty($result->{$carrierParam})
            && 'auctionInc' === $result->{$carrierParam}
            && \XLite\Module\XC\AuctionInc\Main::isSSAvailable()
        ) {
            $result->{$carrierParam} = '';
        }

        if (!empty($result->{$carrierParam}) && 'auctionInc' === $result->{$carrierParam}) {
            $filter = array();
            $filter[] = 'FF%';
            $filter[] = 'NOCHG%';

            // UPS Next Day Air Early AM is a commercial only service.
            // Rather than ask you to implement differential code based
            // on the module Residential setting, lets just eliminate
            // this service method for the XS trial.
            $filter[] = 'UPS_UPSNDE';

            // The two “Saturday” services have special handling in AuctionInc.
            // It would be best just to eliminate these two service methods as well for the XS trial
            $filter[] = 'FEDEX_FDXPOS';
            $filter[] = 'UPS_UPSNDAS';


            foreach (array('DHL', 'FEDEX', 'UPS', 'USPS') as $carrier) {
                $entryPoint = \XLite\Core\Config::getInstance()->XC->AuctionInc->{'entryPoint' . $carrier};
                if (\XLite\Module\XC\AuctionInc\View\FormField\Select\AEntryPoint::STATE_DISABLED === $entryPoint) {
                    $filter[] = $carrier . '%';
                }
            }

            $result->{\XLite\Model\Repo\Shipping\Method::P_AUCTION_INC_FILTER} = $filter;
        }

        return $result;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        $processor = $this->getProcessor();

        return ($processor && 'auctionInc' === $processor->getProcessorId())
            ? static::SORT_TYPE_NONE
            : parent::getSortableType();
    }
}
