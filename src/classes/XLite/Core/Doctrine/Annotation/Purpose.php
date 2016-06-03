<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Doctrine\Annotation;

/**
 * Purpose
 *
 * @Annotation
 */
class Purpose extends \Doctrine\Common\Annotations\Annotation
{
    /**
     * Purpose name 
     * 
     * @var string
     */
    public $name;

    /**
     * Purpose source (default - base property)
     * 
     * @var string
     */
    public $source;
}
