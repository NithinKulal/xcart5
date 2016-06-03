<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Controller\Admin;

/**
 * Reviews controller
 *
 */
class Reviews extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (
            \XLite\Core\Request::getInstance()->isGet()
            && !\XLite\Core\Request::getInstance()->isAJAX()
        ) {
            // Reset 'isNew' status of reviews on page open
            // (ignore this on POST and AJAX requests)
            $this->resetIsNewStatus();
        }

        parent::doNoAction();
    }

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass()
            ?: 'XLite\Module\XC\Reviews\View\ItemsList\Model\Review';
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Products reviews');
    }

    /**
     * Return null since it's common reviews list
     *
     * @return integer
     */
    public function getProductId()
    {
        return 0;
    }

    /**
     * Get date condition parameter (start or end)
     *
     * @param boolean $start Start date flag, otherwise - end date  OPTIONAL
     *
     * @return mixed
     */
    public function getDateCondition($start = true)
    {
        $dates = $this->getCondition(\XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_ADDITION_DATE);
        $n = (true === $start) ? 0 : 1;

        $date = isset($dates[$n]) ? $dates[$n] : LC_START_TIME;
        if ($start && LC_START_TIME == $date) {
            $date -= 86400 * 30;
        }

        return $date;
    }

    /**
     * Get date value for search params
     *
     * @param string  $fieldName Field name (prefix)
     * @param boolean $isEndDate End date flag OPTIONAL
     *
     * @return integer
     */
    public function getDateValue($fieldName, $isEndDate = false)
    {
        $dateValue = \XLite\Core\Request::getInstance()->$fieldName;

        if (isset($dateValue)) {
            $timeValue = $isEndDate ? '23:59:59' : '0:0:0';
            $dateValue = intval(strtotime($dateValue . ' ' . $timeValue));
        } else {
            $dateValue = time();
        }

        return $dateValue;
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionUpdateItemsList()
    {
        parent::doActionUpdateItemsList();

        $productId = \XLite\Core\Request::getInstance()->product_id;
        if ($productId) {
            /** @var \XLite\Module\XC\Reviews\Model\Repo\Review $repo */
            $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review');
            $repo->setUseForMeta($productId, \XLite\Core\Request::getInstance()->useForMeta);
        }
    }

    /**
     * Do action 'delete'
     *
     * @return void
     */
    protected function doActionDelete()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('\XLite\Module\XC\Reviews\Model\Review')->deleteInBatchById($select);
            \XLite\Core\TopMessage::addInfo(
                'Selected reviews have been deleted'
            );

        } else {
            \XLite\Core\TopMessage::addWarning('Please select the reviews first');
        }
    }

    /**
     * Do action 'approve'
     *
     * @return void
     */
    protected function doActionApprove()
    {
        $this->changeReviewStatuses(\XLite\Module\XC\Reviews\Model\Review::STATUS_APPROVED);
    }

    /**
     * Do action 'unapprove'
     *
     * @return void
     */
    protected function doActionUnapprove()
    {
        $this->changeReviewStatuses(\XLite\Module\XC\Reviews\Model\Review::STATUS_PENDING);
    }

    /**
     * Change statuses of the reviews from request
     *
     * @param integer $status New status
     *
     * @return void
     */
    protected function changeReviewStatuses($status)
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            $data = array();
            $properties = array(
                'status' => $status,
            );

            foreach ($select as $id => $tmp) {
                $data[$id] = $properties;
            }

            \XLite\Core\Database::getRepo('\XLite\Module\XC\Reviews\Model\Review')->updateInBatchById($data);
            \XLite\Core\TopMessage::addInfo(
                'Selected reviews have been updated'
            );

        } else {
            \XLite\Core\TopMessage::addWarning('Please select the reviews first');
        }
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        // Prepare dates

        $this->startDate = $this->getDateValue('startDate');
        $this->endDate   = $this->getDateValue('endDate', true);

        if (0 === $this->startDate
            || 0 === $this->endDate
            || $this->startDate > $this->endDate
        ) {
            $date = getdate(time());

            $this->startDate = mktime(0, 0, 0, $date['mon'], 1, $date['year']);
            $this->endDate   = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
        }

        $searchParams = parent::getSearchParams();

        foreach ($searchParams as $requestParam) {
            if (\XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_ADDITION_DATE === $requestParam) {
                $searchParams[$requestParam] = array($this->startDate, $this->endDate);
            } elseif (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $searchParams[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        return $searchParams;
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getSessionSearchConditions()
    {
        $cellName = $this->getSessionCellName();

        $searchParams = parent::getSessionSearchConditions();

        if (!is_array($searchParams)) {
            $searchParams = array();

            $now = time();
            $startDate = $now - 2592000; // One month

            $searchParams['dateRange'] =  date('Y-m-d', $startDate) . ' ~ ' . date('Y-m-d', $now);
            \XLite\Core\Session::getInstance()->$cellName =  $searchParams;
        }

        return $searchParams;
    }

    /**
     * Reset status 'isNew' for all reviews
     *
     * @return void
     */
    protected function resetIsNewStatus()
    {
        $reviews = \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review')->search(
            $this->getResetIsNewSearchCondition()
        );

        if ($reviews) {
            foreach ($reviews as $review) {
                $review->setIsNew(0);
            }

            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Get conditions to search reviews for reset 'isNew' status
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getResetIsNewSearchCondition()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_NEW} = 1;

        return $cnd;
    }

    // }}}
}
