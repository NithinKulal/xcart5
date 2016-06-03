<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Image\Product;

/**
 * Product image
 */
class Image extends \XLite\Model\Repo\Base\Image
{
    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'orderby';

    /**
     * Returns the name of the directory within 'root/images' where images stored
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'product';
    }

}
