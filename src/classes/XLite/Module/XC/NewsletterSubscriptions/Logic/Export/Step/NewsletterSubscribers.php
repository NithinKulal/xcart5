<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NewsletterSubscriptions\Logic\Export\Step;

/**
 * Subscribers
 */
class NewsletterSubscribers extends \XLite\Logic\Export\Step\AStep
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\NewsletterSubscriptions\Model\Subscriber');
    }


    /**
     * Get filename
     *
     * @return string
     */
    protected function getFilename()
    {
        return 'newslettersubscribers.csv';
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
            'Email Address'     => array(static::COLUMN_GETTER => 'getEmailColumnValue'),
            'First Name'        => array(static::COLUMN_GETTER => 'getFirstNameColumnValue'),
            'Last Name'         => array(static::COLUMN_GETTER => 'getLastNameColumnValue'),
        );

        return $columns;
    }

    // }}}

    // {{{ Getters and formatters

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
        return $dataset['model']->getEmail();
    }

    /**
     * Get column value for 'first name' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getFirstNameColumnValue(array $dataset, $name, $i)
    {
        $result = '';

        if ($dataset['model']->getProfile()) {
            $profile = $dataset['model']->getProfile();
            $address = $profile->getBillingAddress() ?: $profile->getShippingAddress();

            $result = $address
                ? trim($address->getFirstname())
                : '';
        }

        return $result;
    }
    /**
     * Get column value for 'first name' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getLastNameColumnValue(array $dataset, $name, $i)
    {
        $result = '';

        if ($dataset['model']->getProfile()) {
            $profile = $dataset['model']->getProfile();
            $address = $profile->getBillingAddress() ?: $profile->getShippingAddress();

            $result = $address
                ? trim($address->getLastname())
                : '';
        }

        return $result;
    }
}
