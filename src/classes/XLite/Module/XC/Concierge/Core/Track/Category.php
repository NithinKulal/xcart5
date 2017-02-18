<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Core\Track;

use XLite\Model\Category as CategoryModel;
use XLite\Module\XC\Concierge\Core\ATrack;

class Category extends ATrack
{
    /**
     * @var CategoryModel
     */
    protected $category;

    /**
     * PaymentMethod constructor.
     *
     * @param string        $event
     * @param CategoryModel $category
     */
    public function __construct($event, $category)
    {
        $this->event    = $event;
        $this->category = $category;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        $category = $this->getCategory();

        return [
            'Category Name' => $category->getName(),
            'Category Id'   => $category->getCategoryId(),
        ];
    }

    /**
     * @return CategoryModel
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param CategoryModel $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }
}
