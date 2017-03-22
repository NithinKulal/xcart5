<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product;

class LowStockAdminNotification extends \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        return [
            $name => [
                'label'    => static::t('Notify administrator if the stock quantity of this product goes below a certain limit'),
                'type'     => 'XLite\View\FormModel\Type\LowStockNotificationType',
                'position' => isset($options['position']) ? $options['position'] : 0,
            ],
        ];
    }

    public static function getData($name, $object)
    {
        return [
            $name => true,
        ];
    }

    public static function populateData($name, $object, $data)
    {
        $object->setLowLimitEnabled($data->{$name});
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return array
     */
    public static function getViewColumns($name, $options)
    {
        return [
            $name => [
                'name'    => static::t('Low stock notification to admin'),
                'orderBy' => isset($options['position']) ? $options['position'] : 0,
            ],
        ];
    }

    /**
     * @param $name
     * @param $object
     *
     * @return array
     */
    public static function getViewValue($name, $object)
    {
        if ($object->getInventoryEnabled()) {
            return ($object->getLowLimitEnabled() && static::isNotificationsEnabled())
                ? static::t('Yes')
                : static::t('No');
        }

        return '';
    }

    /**
     * Check if low limit warning notification disabled
     *
     * @return bool
     */
    protected static function isNotificationsEnabled()
    {
        $notification = \XLite\Core\Database::getRepo('XLite\Model\Notification')->find('low_limit_warning');

        return $notification && $notification->getEnabledForAdmin();
    }
}
