<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\View\Menu\Admin;

/**
 * Menu
 */
abstract class AAdmin extends \XLite\View\Menu\Admin\AAdmin implements \XLite\Base\IDecorator
{
    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        parent::__construct();

        if (!in_array('sales_tax', $this->relatedTargets['tax_classes'])) {
            $this->relatedTargets['tax_classes'][] = 'sales_tax';
            $this->relatedTargets['sales_tax'] = $this->relatedTargets['tax_classes'];
            $this->relatedTargets['sales_tax'][] = 'tax_classes';
        }

    }
}
