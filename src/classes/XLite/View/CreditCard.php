<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\View;


class CreditCard extends \XLite\View\AView
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'checkout/css/credit_card.css';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'checkout/js/credit_card_form.js';

        return $list;
    }

    /**
     * Get years array for expired year field
     *
     * @return array
     */
    protected function getExpiredYears()
    {
        $years = array();

        $currentYear = date("Y");

        for ($i = 0; $i < 10; $i++) {
            $year = (int)$currentYear + $i;
            $years[$year] = $year;
        }

        return $years;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'checkout/credit_card_form.twig';
    }
}