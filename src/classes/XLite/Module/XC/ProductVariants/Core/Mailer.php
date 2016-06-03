<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Core;

/**
 * Mailer
 */
abstract class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * New mail type
     */
    const TYPE_LOW_VARIANT_LIMIT_WARNING = 'low_variant_limit_warning';

    /**
     * Send contact us message
     *
     * @param array $data Data
     *
     * @return string | null
     */
    public static function sendLowVariantLimitWarningAdmin(array $data)
    {
        static::register('data', $data);

        static::compose(
            static::TYPE_LOW_VARIANT_LIMIT_WARNING,
            static::getOrdersDepartmentMail(),
            static::getSiteAdministratorMail(),
            'modules/XC/ProductVariants/low_variant_limit_warning',
            array(),
            true,
            \XLite::ADMIN_INTERFACE,
            static::getMailer()->getLanguageCode(\XLite::ADMIN_INTERFACE)
        );
    }
}
