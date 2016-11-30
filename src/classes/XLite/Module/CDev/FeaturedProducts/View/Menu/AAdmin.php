<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\View\Menu;

/**
 * Abstract admin menu
 */
abstract class AAdmin extends \XLite\View\Menu\Admin\AAdmin implements \XLite\Base\IDecorator
{
    /**
     * @param array $params
     */
    public function __construct(array $params = array())
    {
        $this->relatedTargets[\XLite\Core\Request::getInstance()->id ? 'categories' : 'front_page'][] = 'featured_products';

        parent::__construct();
    }
}
