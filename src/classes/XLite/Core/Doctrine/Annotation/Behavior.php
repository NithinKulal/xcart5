<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Doctrine\Annotation;

/**
 * Behavior 
 *
 * @Annotation
 */
class Behavior extends \Doctrine\Common\Annotations\Annotation
{
    /**
     * Behaviors list 
     * 
     * @var array
     */
    public $list = array();
}
