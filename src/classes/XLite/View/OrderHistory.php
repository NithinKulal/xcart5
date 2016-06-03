<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Order history widget
 *
 * @ListChild (list="order", weight="150", zone="admin")
 */
class OrderHistory extends \XLite\View\AView
{
    /**
     * Widget parameters
     */
    const PARAM_ORDER = 'order';

    /**
     * Cached blocks
     *
     * @var   array
     */
    protected $cachedBlocks;

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->getOrder()->getOrderId();
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'order/history/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'order/history/script.js';

        return $list;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/history/body.twig';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getOrder()
            && 0 < count($this->getOrderHistoryEventsBlock());
    }

    /**
     * Get blocks for the events of order
     *
     * @return array
     */
    protected function getOrderHistoryEventsBlock()
    {
        if (null === $this->cachedBlocks) {
            $this->cachedBlocks = $this->defineOrderHistoryEventsBlock();
        }

        return $this->cachedBlocks;
    }

    /**
     * Define blocks for the events of order
     *
     * @return array
     */
    protected function defineOrderHistoryEventsBlock()
    {
        $result = array();

        $list = \XLite\Core\Database::getRepo('XLite\Model\OrderHistoryEvents')->findAllByOrder($this->getOrder());
        foreach ($list as $event) {
            $result[$this->getDayDate($event->getDate())][] = $event;
        }

        return $result;
    }


    /**
     * Return true if event has comment or details
     *
     * @param \XLite\Model\OrderHistoryEvents $event Event object
     *
     * @return boolean
     */
    protected function isDisplayDetails(\XLite\Model\OrderHistoryEvents $event)
    {
        return $event->getComment() || $this->getDetails($event);
    }

    /**
     * Date getter
     *
     * @param \XLite\Model\OrderHistoryEvents $event Event
     *
     * @return string
     */
    protected function getDate(\XLite\Model\OrderHistoryEvents $event)
    {
        return \XLite\Core\Converter::formatDayTime($event->getDate());
    }

    /**
     * Description getter
     *
     * @param \XLite\Model\OrderHistoryEvents $event Event
     *
     * @return string
     */
    protected function getDescription(\XLite\Model\OrderHistoryEvents $event)
    {
        return $event->getDescription();
    }

    /**
     * Comment getter
     *
     * @param \XLite\Model\OrderHistoryEvents $event Event
     *
     * @return string
     */
    protected function getComment(\XLite\Model\OrderHistoryEvents $event)
    {
        $result = $event->getComment();

        $codes = array(
            \XLite\Core\OrderHistory::CODE_ORDER_EDITED,
            \XLite\Core\OrderHistory::CODE_CHANGE_NOTES_ORDER,
            \XLite\Core\OrderHistory::CODE_CHANGE_CUSTOMER_NOTES_ORDER,
        );

        if (in_array($event->getCode(), $codes)) {
            $changes = unserialize($result);
            if (is_array($changes)) {
                $widget = new \XLite\View\OrderEditHistoryData(
                    array(
                        \XLite\View\OrderEditHistoryData::PARAM_CHANGES => $changes,
                    )
                );
                $widget->init();
                $result = $widget->getContent();
            }
        }

        return $result;
    }

    /**
     * Details getter
     *
     * @param \XLite\Model\OrderHistoryEvents $event Event
     *
     * @return array
     */
    protected function getDetails(\XLite\Model\OrderHistoryEvents $event)
    {
        $list = array();

        $columnId = 0;

        foreach ($event->getDetails() as $cell) {
            if ($cell->getName()) {
                $list[$columnId][] = $cell;
                $columnId++;
            }

            if ($this->getColumnsNumber() <= $columnId) {
                $columnId = 0;
            }
        }

        return $list;
    }

    /**
     * Get number of columns to display event details
     *
     * @return integer
     */
    protected function getColumnsNumber()
    {
        return 3;
    }

    /**
     * Get day of the given date
     *
     * @param integer $date Date (UNIX timestamp)
     *
     * @return string
     */
    protected function getDayDate($date)
    {
        return \XLite\Core\Converter::formatDate($date);
    }

    /**
     * Return header of the block
     *
     * @param string $index Index
     *
     * @return string
     */
    protected function getHeaderBlock($index)
    {
        return $index;
    }
}
