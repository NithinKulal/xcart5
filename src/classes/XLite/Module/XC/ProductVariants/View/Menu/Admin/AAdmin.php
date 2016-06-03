<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Menu\Admin;

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

        if (!in_array('product_variant', $this->relatedTargets['product_list'])) {
            $this->relatedTargets['product_list'][] = 'product_variant';
        }
    }
}
