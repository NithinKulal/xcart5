<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\Model;

use XLite\Module\CDev\PINCodes\View\FormField\Input\Text\Integer\ProductQuantity as ProductQuantityInput;

/**
 * Product view model
 */
abstract class InventoryTracking extends \XLite\View\Model\InventoryTracking implements \XLite\Base\IDecorator
{
    /**
     * Disable some element if product has manual pinCodes
     */
    function __construct ()
    {
        if ($this->getModelObject()->hasManualPinCodes()) {
            $this->schemaDefault['inventoryEnabled'][static::SCHEMA_ATTRIBUTES] = array(
                'disabled' => 'disabled'
            );

            $this->schemaDefault['amount'][static::SCHEMA_ATTRIBUTES] = array(
                'disabled' => 'disabled'
            );
            $this->schemaDefault['amount'][self::SCHEMA_HELP] =
                'Quantity in stock is determined by the amount of the remaining PIN codes.';
        }

        parent::__construct();
    }
}
