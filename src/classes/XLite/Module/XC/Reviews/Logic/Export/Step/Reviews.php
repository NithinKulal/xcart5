<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Logic\Export\Step;

/**
 * Reviews
 */
class Reviews extends \XLite\Logic\Export\Step\AStep
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review');
    }


    /**
     * Get filename
     *
     * @return string
     */
    protected function getFilename()
    {
        return 'reviews.csv';
    }

    // }}}

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = array(
            'product'       => array(),
            'review'        => array(),
            'rating'        => array(),
            'additionDate'  => array(),
            'reviewerName'  => array(),
            'email'         => array(),
            'status'        => array(),
            'ip'            => array(),
            'useForMeta'    => array(),
        );

        return $columns;
    }

    // }}}

    // {{{ Getters and formatters

    /**
     * Get column value for 'product' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getProductColumnValue(array $dataset, $name, $i)
    {
        $product = $dataset['model']->getProduct();
        $sku = $product->getSKU();

        return $sku ?: 'Unknown';
    }

    /**
     * Get column value for 'review' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getReviewColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'review');
    }

    /**
     * Get column value for 'rating' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getRatingColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'rating');
    }

    /**
     * Get column value for 'additionDate' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getAdditionDateColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'additionDate');
    }

    /**
     * Format 'additionDate' field value
     *
     * @param mixed  $value   Value
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return string
     */
    protected function formatAdditionDateColumnValue($value, array $dataset, $name)
    {
        return $this->formatTimestamp($value);
    }

    /**
     * Get column value for 'reviewerName' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getReviewerNameColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'reviewerName');
    }

    /**
     * Get column value for 'email' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getEmailColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'email');
    }

    /**
     * Get column value for 'status' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getStatusColumnValue(array $dataset, $name, $i)
    {
        $status = $dataset['model']->isApproved();
        
        return $status ? 'Approved' : 'Pending';
    }

    /**
     * Get column value for 'ip' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getIpColumnValue(array $dataset, $name, $i)
    {
        $ip = $dataset['model']->getIp();

        if (is_resource($ip)) {

            $result = stream_get_contents($ip);

            if (!$result) {
                $result = null;

            } elseif ((string)((int)$result) === $result) {
                // To support old style IP values (integer)
                $result = long2ip($result);

            } else {
                $result = $result ? inet_ntop($result) : null;
            }

        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Get column value for 'useForMeta' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getUseForMetaColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'useForMeta');
    }

    // }}}
}
