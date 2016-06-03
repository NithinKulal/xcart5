<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\IdealPayments\View\FormField\Select;

/**
 * Issuer selector widget
 */
class Issuer extends \XLite\View\FormField\Select\Regular
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();

        $processor = new \XLite\Module\XC\IdealPayments\Model\Payment\Processor\IdealProfessional();

        $issuers = $processor->doIssuerRequest();

        if (is_array($issuers)) {
            foreach ($issuers as $key => $value) {
                $list[$key] = $value;
            }
        }

        return $list;
    }
}
